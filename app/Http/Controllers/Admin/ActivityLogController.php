<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Time filter presets
        $timeFilter = $request->get('time', 'all');
        if ($timeFilter !== 'all') {
            $dateFrom = match($timeFilter) {
                'today' => now()->startOfDay(),
                'yesterday' => now()->subDay()->startOfDay(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                'last_month' => now()->subMonth()->startOfMonth(),
                default => null,
            };
            $dateTo = $timeFilter === 'yesterday' ? now()->subDay()->endOfDay() : null;
            $dateTo = $timeFilter === 'last_month' ? now()->subMonth()->endOfMonth() : $dateTo;

            if ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                }
            }
        } else {
            // Custom date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['created_at', 'action', 'module', 'ip_address', 'type'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $perPage = $request->get('per_page', 25);
        $logs = $query->paginate($perPage)->withQueryString();

        // Get unique values for filters
        $modules = ActivityLog::distinct('module')->pluck('module');
        $actions = ActivityLog::distinct('action')->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'modules', 'actions', 'sortBy', 'sortDir', 'timeFilter'));
    }

    public function show(ActivityLog $log)
    {
        return view('admin.activity-logs.show', compact('log'));
    }

    public function destroy(ActivityLog $log)
    {
        $log->delete();

        return back()->with('success', 'Log berhasil dihapus');
    }

    public function clear(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $count = ActivityLog::count();
        ActivityLog::truncate();

        return back()->with('success', "Berhasil menghapus {$count} log aktivitas");
    }
}
