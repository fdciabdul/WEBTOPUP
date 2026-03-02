<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\BonusFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $pendingTransactions = Transaction::where('user_id', $user->id)->where('status', 'pending')->count();
        $completedTransactions = Transaction::where('user_id', $user->id)->where('status', 'completed')->count();
        $totalProducts = Product::where('status', 'active')->count();

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->limit(5)
            ->get();

        // Best sellers: top products by transaction count for this user
        $bestSellers = Product::withCount(['transactions' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->whereHas('transactions', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('transactions_count')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'pendingTransactions', 'completedTransactions', 'totalProducts',
            'recentTransactions', 'bestSellers'
        ));
    }

    public function transactions(Request $request)
    {
        $userId = auth()->id();

        $query = Transaction::where('user_id', $userId)
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

        // Status counts for filter tabs
        $allCount = Transaction::where('user_id', $userId)->count();
        $pendingCount = Transaction::where('user_id', $userId)->where('status', 'pending')->count();
        $processingCount = Transaction::where('user_id', $userId)->whereIn('status', ['processing', 'paid'])->count();
        $completedCount = Transaction::where('user_id', $userId)->where('status', 'completed')->count();
        $failedCount = Transaction::where('user_id', $userId)->whereIn('status', ['failed', 'cancelled', 'expired'])->count();

        return view('dashboard.transactions', compact(
            'transactions', 'allCount', 'pendingCount', 'processingCount', 'completedCount', 'failedCount'
        ));
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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Password berhasil diubah');
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|in:midtrans,bank_transfer',
        ]);

        // TODO: Integrate with payment gateway
        return back()->with('success', 'Fitur top up saldo akan segera tersedia');
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        auth()->logout();
        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Akun Anda berhasil dihapus');
    }
}
