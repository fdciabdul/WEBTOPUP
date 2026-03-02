@extends('layouts.admin')

@section('title', 'Detail Log Aktivitas')
@section('page-title', 'Detail Log Aktivitas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-slide-up-fade">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Detail Log Aktivitas</h2>
            <p class="text-sm text-slate-500">Informasi lengkap aktivitas #{{ $log->id }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition flex items-center gap-2">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <form action="{{ route('admin.activity-logs.destroy', $log) }}" method="POST" class="inline"
                  onsubmit="return confirm('Yakin ingin menghapus log ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition flex items-center gap-2">
                    <i class="ri-delete-bin-line"></i> Hapus Log
                </button>
            </form>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="glass-panel rounded-[2rem] overflow-hidden animate-slide-up-fade delay-100">
        <div class="bg-gradient-to-r from-brand-600 to-blue-500 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white">
                    <i class="{{ $log->action_icon }} text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-extrabold text-sm uppercase tracking-wide">{{ $log->action }}</h3>
                    <p class="text-blue-100 text-xs">{{ $log->module }}</p>
                </div>
            </div>
            @php
                $typeColors = [
                    'info' => 'bg-blue-100 text-blue-600',
                    'success' => 'bg-emerald-100 text-emerald-600',
                    'warning' => 'bg-amber-100 text-amber-600',
                    'error' => 'bg-red-100 text-red-600',
                ];
            @endphp
            <span class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $typeColors[$log->type] ?? $typeColors['info'] }}">
                {{ ucfirst($log->type) }}
            </span>
        </div>

        <div class="p-6 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Waktu -->
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-time-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Waktu</p>
                        <p class="text-sm font-bold text-slate-800">{{ $log->created_at->format('d M Y, H:i:s') }}</p>
                        <p class="text-xs text-slate-500">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- User -->
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-user-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">User</p>
                        @if($log->user)
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user->name) }}&background=0284c7&color=fff&size=32"
                                     class="w-8 h-8 rounded-full" alt="">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $log->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $log->user->role ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm font-bold text-slate-500">System</p>
                        @endif
                    </div>
                </div>

                <!-- Action -->
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-flashlight-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Action</p>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                <i class="{{ $log->action_icon }}"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 uppercase">{{ $log->action }}</span>
                        </div>
                    </div>
                </div>

                <!-- Module -->
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-apps-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Module</p>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">{{ $log->module }}</span>
                    </div>
                </div>

                <!-- IP Address -->
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-global-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">IP Address</p>
                        <code class="text-sm bg-slate-100 px-3 py-1 rounded-lg text-slate-700 font-bold">{{ $log->ip_address ?? '-' }}</code>
                    </div>
                </div>

                <!-- Type -->
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-price-tag-3-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Type</p>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $typeColors[$log->type] ?? $typeColors['info'] }}">
                            {{ ucfirst($log->type) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-file-text-line text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Deskripsi</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $log->description }}</p>
                    </div>
                </div>
            </div>

            <!-- User Agent -->
            @if($log->user_agent)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="ri-device-line text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">User Agent</p>
                        <code class="text-xs bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-slate-600 block break-all leading-relaxed">{{ $log->user_agent }}</code>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Data Changes -->
    @if($log->old_data || $log->new_data)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Old Data -->
        @if($log->old_data)
        <div class="glass-panel rounded-[2rem] overflow-hidden animate-slide-up-fade delay-200">
            <div class="px-6 py-4 bg-red-50 border-b border-red-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500">
                    <i class="ri-arrow-left-circle-line text-lg"></i>
                </div>
                <h4 class="font-extrabold text-red-700 text-sm">Data Sebelumnya</h4>
            </div>
            <div class="p-6 bg-white">
                <div class="space-y-3">
                    @foreach($log->old_data as $key => $value)
                    <div class="flex items-start gap-3 py-2 border-b border-slate-50 last:border-0">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide min-w-[120px] pt-0.5">{{ $key }}</span>
                        <span class="text-sm text-slate-700 break-all">
                            @if(is_array($value) || is_object($value))
                                <code class="text-xs bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg block whitespace-pre-wrap">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- New Data -->
        @if($log->new_data)
        <div class="glass-panel rounded-[2rem] overflow-hidden animate-slide-up-fade delay-300">
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-500">
                    <i class="ri-arrow-right-circle-line text-lg"></i>
                </div>
                <h4 class="font-extrabold text-emerald-700 text-sm">Data Sesudahnya</h4>
            </div>
            <div class="p-6 bg-white">
                <div class="space-y-3">
                    @foreach($log->new_data as $key => $value)
                    <div class="flex items-start gap-3 py-2 border-b border-slate-50 last:border-0">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide min-w-[120px] pt-0.5">{{ $key }}</span>
                        <span class="text-sm text-slate-700 break-all">
                            @if(is_array($value) || is_object($value))
                                <code class="text-xs bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg block whitespace-pre-wrap">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
