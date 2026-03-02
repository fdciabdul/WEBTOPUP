<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        $dateRange = $this->getDateRange($period);

        // Calculate current period stats
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::where('status', 'completed')->sum('total_amount');
        $totalMembers = User::where('role', 'member')->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        // Calculate growth percentages compared to last month
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $lastMonthTransactions = Transaction::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $lastMonthRevenue = Transaction::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->where('status', 'completed')
            ->sum('total_amount');
        $lastMonthMembers = User::where('role', 'member')
            ->where('created_at', '<', now()->startOfMonth())
            ->count();

        $transactionsGrowth = $lastMonthTransactions > 0
            ? round((($totalTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100, 1)
            : 0;
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;
        $membersGrowth = $lastMonthMembers > 0
            ? round((($totalMembers - $lastMonthMembers) / $lastMonthMembers) * 100, 1)
            : 0;

        // Transaction status counts
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $processingTransactions = Transaction::where('status', 'processing')->count();
        $completedToday = Transaction::where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();
        $failedTransactions = Transaction::where('status', 'failed')->count();

        $stats = [
            'total_orders' => Transaction::whereBetween('created_at', $dateRange)->count(),
            'total_items_sold' => Transaction::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->sum('quantity'),
            'total_revenue' => Transaction::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'pending_orders' => $pendingTransactions,
            'total_products' => $totalProducts,
            'total_members' => $totalMembers,
            'new_members_today' => User::where('role', 'member')
                ->whereDate('created_at', today())
                ->count(),
        ];

        $topCategories = Category::withCount(['products' => function ($query) use ($dateRange) {
                $query->whereHas('transactions', function ($q) use ($dateRange) {
                    $q->whereBetween('created_at', $dateRange)
                      ->where('status', 'completed');
                });
            }])
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        $topProducts = Product::withCount(['transactions' => function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange)
                      ->where('status', 'completed');
            }])
            ->withSum(['transactions' => function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange)
                      ->where('status', 'completed');
            }], 'quantity')
            ->orderBy('transactions_count', 'desc')
            ->limit(10)
            ->get();

        $recentTransactions = Transaction::with(['user', 'product'])
            ->latest()
            ->limit(10)
            ->get();

        $recentMembers = User::where('role', 'member')
            ->latest()
            ->limit(10)
            ->get();

        $chartData = $this->getChartData($period);

        return view('admin.dashboard', compact(
            'stats',
            'totalTransactions',
            'transactionsGrowth',
            'totalRevenue',
            'revenueGrowth',
            'totalMembers',
            'membersGrowth',
            'totalProducts',
            'activeProducts',
            'pendingTransactions',
            'processingTransactions',
            'completedToday',
            'failedTransactions',
            'topCategories',
            'topProducts',
            'recentTransactions',
            'recentMembers',
            'chartData',
            'period'
        ));
    }

    protected function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'all' => [now()->subYears(10), now()->endOfDay()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    protected function getChartData(string $period): array
    {
        $days = match($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            default => 7,
        };

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');

            $revenue = Transaction::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $orders = Transaction::whereDate('created_at', $date)->count();

            $data['labels'][] = now()->subDays($i)->format('M d');
            $data['revenue'][] = $revenue;
            $data['orders'][] = $orders;
        }

        return $data;
    }
}
