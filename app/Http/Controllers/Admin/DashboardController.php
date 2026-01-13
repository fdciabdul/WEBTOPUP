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

        $stats = [
            'total_orders' => Transaction::whereBetween('created_at', $dateRange)->count(),
            'total_items_sold' => Transaction::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->sum('quantity'),
            'total_revenue' => Transaction::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'pending_orders' => Transaction::where('status', 'pending')->count(),
            'total_products' => Product::active()->count(),
            'total_members' => User::where('role', 'member')->count(),
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

        $recentUsers = User::where('role', 'member')
            ->latest()
            ->limit(10)
            ->get();

        $chartData = $this->getChartData($period);

        return view('admin.dashboard', compact(
            'stats',
            'topCategories',
            'topProducts',
            'recentTransactions',
            'recentUsers',
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
