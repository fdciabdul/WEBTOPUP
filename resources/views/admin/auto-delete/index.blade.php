@extends('layouts.admin')

@section('title', 'Auto Delete')
@section('page-title', 'Pengaturan Auto Delete')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Auto Delete</h2>
            <p class="text-sm text-slate-500">Atur penghapusan otomatis data lama untuk menghemat storage</p>
        </div>
    </div>

    <form action="{{ route('admin.auto-delete.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="glass-panel rounded-[2rem] overflow-hidden">
            <div class="bg-gradient-to-r from-rose-500 to-red-500 p-5 flex items-center gap-3 text-white">
                <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                    <i class="ri-delete-bin-2-fill text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Pengaturan Auto Delete</h3>
                    <p class="text-xs text-rose-100 font-medium">Konfigurasikan penghapusan otomatis</p>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                                <th class="pb-4 pr-4">Data</th>
                                <th class="pb-4 pr-4 text-center">Status</th>
                                <th class="pb-4 pr-4 text-center">Hapus Setelah</th>
                                <th class="pb-4 pr-4 text-center">Terakhir Dijalankan</th>
                                <th class="pb-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($settings as $index => $setting)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 pr-4">
                                    <input type="hidden" name="settings[{{ $index }}][id]" value="{{ $setting->id }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800">{{ $setting->label }}</h4>
                                            <p class="text-xs text-slate-500">{{ $setting->description }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 pr-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[{{ $index }}][is_enabled]" value="1"
                                               class="sr-only peer" {{ $setting->is_enabled ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                    </label>
                                </td>
                                <td class="py-4 pr-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <input type="number" name="settings[{{ $index }}][days]" value="{{ $setting->days }}"
                                               class="w-20 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-center text-sm font-bold text-slate-700 focus:border-brand-500 focus:outline-none" min="1" max="365">
                                        <span class="text-xs text-slate-500">hari</span>
                                    </div>
                                </td>
                                <td class="py-4 pr-4 text-center">
                                    @if($setting->last_run_at)
                                        <div class="text-xs">
                                            <div class="font-bold text-slate-700">{{ $setting->last_run_at->format('d M Y, H:i') }}</div>
                                            <div class="text-slate-500">{{ $setting->last_deleted_count }} data dihapus</div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Belum pernah</span>
                                    @endif
                                </td>
                                <td class="py-4 text-center">
                                    <form action="{{ route('admin.auto-delete.run', $setting->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menjalankan auto delete untuk {{ $setting->label }} sekarang?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                                            <i class="ri-play-fill mr-1"></i> Run Now
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    <!-- Info Card -->
    <div class="glass-panel rounded-2xl p-5 border-l-4 border-amber-500">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 flex-shrink-0">
                <i class="ri-information-line text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-1">Informasi Auto Delete</h4>
                <p class="text-sm text-slate-600">Auto delete akan dijalankan setiap hari pada pukul 00:00 WIB melalui scheduler. Pastikan cron job sudah dikonfigurasi dengan benar.</p>
                <code class="block mt-2 text-xs bg-slate-100 px-3 py-2 rounded-lg text-slate-700">* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</code>
            </div>
        </div>
    </div>
</div>
@endsection
