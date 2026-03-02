<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecuritySetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index()
    {
        $settings = SecuritySetting::all()->keyBy('key');

        return view('admin.security.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'login_max_attempts' => 'required|integer|min:1|max:20',
            'login_lockout_duration' => 'required|integer|min:1|max:1440',
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'force_https' => 'nullable|boolean',
            'ip_whitelist' => 'nullable|string|max:1000',
            'maintenance_mode' => 'nullable|boolean',
            'recaptcha_enabled' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            if (is_bool($value) || $key === 'force_https' || $key === 'maintenance_mode' || $key === 'recaptcha_enabled') {
                $value = $value ? '1' : '0';
            }
            SecuritySetting::set($key, $value ?? '');
        }

        ActivityLog::log('update', 'security', 'Mengubah pengaturan keamanan', null, $validated, 'success');

        return back()->with('success', 'Pengaturan keamanan berhasil disimpan');
    }
}
