<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoDeleteSetting;
use App\Models\ActivityLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AutoDeleteController extends Controller
{
    public function index()
    {
        $settings = AutoDeleteSetting::all();

        return view('admin.auto-delete.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:auto_delete_settings,id',
            'settings.*.is_enabled' => 'nullable|boolean',
            'settings.*.days' => 'required|integer|min:1|max:365',
        ]);

        foreach ($validated['settings'] as $data) {
            AutoDeleteSetting::where('id', $data['id'])->update([
                'is_enabled' => isset($data['is_enabled']) && $data['is_enabled'],
                'days' => $data['days'],
            ]);
        }

        ActivityLog::log('update', 'auto_delete', 'Mengubah pengaturan auto delete', null, null, 'success');

        return back()->with('success', 'Pengaturan auto delete berhasil disimpan');
    }

    public function runNow(AutoDeleteSetting $setting)
    {
        $deletedCount = 0;
        $cutoffDate = Carbon::now()->subDays($setting->days);

        switch ($setting->key) {
            case 'activity_logs':
                $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();
                break;

            case 'failed_transactions':
                $deletedCount = Transaction::whereIn('status', ['failed', 'expired'])
                    ->where('created_at', '<', $cutoffDate)
                    ->delete();
                break;

            case 'pending_transactions':
                $deletedCount = Transaction::where('status', 'pending')
                    ->where('created_at', '<', $cutoffDate)
                    ->delete();
                break;
        }

        $setting->update([
            'last_run_at' => now(),
            'last_deleted_count' => $deletedCount,
        ]);

        ActivityLog::log('delete', 'auto_delete', "Auto delete manual: {$setting->label} - {$deletedCount} data dihapus", null, null, 'warning');

        return back()->with('success', "Berhasil menghapus {$deletedCount} data {$setting->label}");
    }
}
