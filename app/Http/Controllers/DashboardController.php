<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BonusFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $stats = [
            'balance' => $user->balance,
            'total_transactions' => $user->total_transactions,
            'total_spending' => $user->total_spending,
            'level' => $user->level,
        ];

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentTransactions'));
    }

    public function transactions(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with('product');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20);

        return view('dashboard.transactions', compact('transactions'));
    }

    public function transactionDetail($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->with('product')
            ->firstOrFail();

        return view('dashboard.transaction-detail', compact('transaction'));
    }

    public function balance()
    {
        $user = auth()->user();

        $balanceHistories = $user->balanceHistories()
            ->latest()
            ->paginate(20);

        return view('dashboard.balance', compact('user', 'balanceHistories'));
    }

    public function bonusFiles()
    {
        $user = auth()->user();

        $bonusFiles = BonusFile::active()
            ->forLevel($user->level)
            ->get();

        return view('dashboard.bonus-files', compact('bonusFiles'));
    }

    public function downloadBonusFile(BonusFile $bonusFile)
    {
        $user = auth()->user();

        $levels = ['visitor', 'reseller', 'reseller_vip', 'reseller_vvip'];
        $userLevelIndex = array_search($user->level, $levels);
        $fileLevelIndex = array_search($bonusFile->required_level, $levels);

        if ($userLevelIndex < $fileLevelIndex) {
            abort(403, 'You do not have access to this file');
        }

        $bonusFile->incrementDownload();

        return Storage::download($bonusFile->file_path, basename($bonusFile->file_path));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Profile updated successfully');
    }
}
