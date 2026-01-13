<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'member');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $members = $query->withCount('transactions')
            ->withSum('transactions', 'total_amount')
            ->latest()
            ->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'level' => 'required|in:visitor,reseller,reseller_vip,reseller_vvip',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'member';

        User::create($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Member created successfully');
    }

    public function show(User $member)
    {
        $member->load(['transactions' => function ($query) {
            $query->latest()->limit(20);
        }, 'balanceHistories' => function ($query) {
            $query->latest()->limit(20);
        }]);

        return view('admin.members.show', compact('member'));
    }

    public function edit(User $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'phone' => 'required|string|unique:users,phone,' . $member->id,
            'password' => 'nullable|string|min:8',
            'level' => 'required|in:visitor,reseller,reseller_vip,reseller_vvip',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $member->update($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Member updated successfully');
    }

    public function destroy(User $member)
    {
        if ($member->transactions()->count() > 0) {
            return redirect()->route('admin.members.index')
                ->with('error', 'Cannot delete member with transaction history');
        }

        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Member deleted successfully');
    }

    public function addBalance(Request $request, User $member)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $member->addBalance(
            $validated['amount'],
            'manual_topup',
            null,
            $validated['description'] ?? 'Balance added by admin'
        );

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Balance added successfully');
    }

    public function deductBalance(Request $request, User $member)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if (!$member->hasBalance($validated['amount'])) {
            return redirect()->route('admin.members.show', $member)
                ->with('error', 'Insufficient balance');
        }

        $member->deductBalance(
            $validated['amount'],
            'manual_deduction',
            null,
            $validated['description'] ?? 'Balance deducted by admin'
        );

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Balance deducted successfully');
    }
}
