@extends('layouts.admin')

@section('title', 'Keamanan')
@section('page-title', 'Pengaturan Keamanan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pengaturan Keamanan</h2>
            <p class="text-sm text-slate-500">Atur keamanan website dan akses admin</p>
        </div>
    </div>

    <form action="{{ route('admin.security.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Login Security -->
            <div class="glass-panel rounded-[2rem] overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-5 flex items-center gap-3 text-white">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="ri-lock-password-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Login Security</h3>
                        <p class="text-xs text-amber-100 font-medium">Proteksi akses login</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Max Login Attempts</label>
                        <div class="flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <i class="ri-error-warning-line text-amber-500 mr-3"></i>
                            <input type="number" name="login_max_attempts" value="{{ $settings['login_max_attempts']->value ?? 5 }}"
                                   class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm" min="1" max="20">
                            <span class="text-xs text-slate-400 ml-2">percobaan</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Jumlah maksimal percobaan login sebelum diblokir</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Lockout Duration</label>
                        <div class="flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <i class="ri-timer-line text-amber-500 mr-3"></i>
                            <input type="number" name="login_lockout_duration" value="{{ $settings['login_lockout_duration']->value ?? 15 }}"
                                   class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm" min="1" max="1440">
                            <span class="text-xs text-slate-400 ml-2">menit</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Durasi blokir setelah melebihi batas login</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Session Lifetime</label>
                        <div class="flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <i class="ri-time-line text-amber-500 mr-3"></i>
                            <input type="number" name="session_lifetime" value="{{ $settings['session_lifetime']->value ?? 120 }}"
                                   class="bg-transparent w-full outline-none font-bold text-slate-700 text-sm" min="1" max="1440">
                            <span class="text-xs text-slate-400 ml-2">menit</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Durasi session aktif sebelum auto logout</p>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="ri-robot-line text-xl text-amber-500"></i>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Enable reCAPTCHA</h4>
                                <p class="text-xs text-slate-500">Aktifkan reCAPTCHA pada form login</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="recaptcha_enabled" value="1" class="sr-only peer"
                                   {{ ($settings['recaptcha_enabled']->value ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Access Control -->
            <div class="glass-panel rounded-[2rem] overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-rose-500 p-5 flex items-center gap-3 text-white">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="ri-shield-check-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Access Control</h3>
                        <p class="text-xs text-red-100 font-medium">Kontrol akses website</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="ri-lock-line text-xl text-red-500"></i>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Force HTTPS</h4>
                                <p class="text-xs text-slate-500">Paksa semua request menggunakan HTTPS</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="force_https" value="1" class="sr-only peer"
                                   {{ ($settings['force_https']->value ?? '1') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <i class="ri-tools-line text-xl text-red-500"></i>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Maintenance Mode</h4>
                                <p class="text-xs text-slate-500">Aktifkan mode maintenance</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer"
                                   {{ ($settings['maintenance_mode']->value ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                        </label>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">IP Whitelist (Admin Access)</label>
                        <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                            <textarea name="ip_whitelist" rows="4"
                                      class="bg-transparent w-full outline-none font-mono text-slate-700 text-sm resize-none"
                                      placeholder="192.168.1.1&#10;10.0.0.1">{{ $settings['ip_whitelist']->value ?? '' }}</textarea>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Satu IP per baris. Kosongkan untuk mengizinkan semua IP.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-6">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all flex items-center gap-2">
                <i class="ri-save-line text-lg"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
