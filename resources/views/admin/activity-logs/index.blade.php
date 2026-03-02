@extends('layouts.admin')

@section('title', 'Log Activity')
@section('page-title', 'Log Aktivitas')

@push('styles')
<style>
    .table-container { max-height: 65vh; overflow-y: auto; }
    .sticky-th { position: sticky; top: 0; z-index: 20; }
    .header-solid { background: #0033AA; color: white; }
    .sort-icon { opacity: 0.4; transition: opacity 0.2s; }
    th:hover .sort-icon { opacity: 1; }
    .sort-active .sort-icon { opacity: 1; }
    @keyframes popup { 0% { opacity: 0; transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
    .animate-popup { animation: popup 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-slide-up-fade">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Log Aktivitas</h2>
            <p class="text-sm text-slate-500">Pantau semua aktivitas yang terjadi di sistem</p>
        </div>
        <button onclick="document.getElementById('clear-modal').classList.remove('hidden')" class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition flex items-center gap-2">
            <i class="ri-delete-bin-line"></i> Hapus Semua Log
        </button>
    </div>

    <!-- Filters -->
    <div class="glass-panel rounded-2xl p-4 animate-slide-up-fade delay-100">
        <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="flex flex-wrap gap-3" id="log-filter-form">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
            @endif

            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user, IP, event..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            <select name="time" onchange="document.getElementById('log-filter-form').submit()" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:border-brand-500 focus:outline-none">
                <option value="all" {{ ($timeFilter ?? 'all') == 'all' ? 'selected' : '' }}>All Time</option>
                <option value="today" {{ ($timeFilter ?? '') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ ($timeFilter ?? '') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="week" {{ ($timeFilter ?? '') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ ($timeFilter ?? '') == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ ($timeFilter ?? '') == 'last_month' ? 'selected' : '' }}>Last Month</option>
            </select>

            <select name="module" onchange="document.getElementById('log-filter-form').submit()" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:border-brand-500 focus:outline-none">
                <option value="">Semua Module</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                @endforeach
            </select>

            <select name="type" onchange="document.getElementById('log-filter-form').submit()" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:border-brand-500 focus:outline-none">
                <option value="">Semua Type</option>
                <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>Info</option>
                <option value="success" {{ request('type') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="error" {{ request('type') === 'error' ? 'selected' : '' }}>Error</option>
            </select>

            <div class="relative w-full md:w-28">
                <select name="per_page" onchange="document.getElementById('log-filter-form').submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl pl-4 pr-8 py-2.5 text-sm font-bold text-slate-600 focus:border-brand-500 focus:outline-none cursor-pointer">
                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 Data</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                </select>
                <i class="ri-list-settings-line absolute right-3 top-3 text-slate-400 pointer-events-none"></i>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition">
                <i class="ri-filter-line mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'module', 'action', 'type', 'time', 'date_from', 'date_to']))
                <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition">
                    <i class="ri-close-line"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Logs Table -->
    <div class="glass-panel rounded-[2rem] p-1 overflow-hidden animate-slide-up-fade delay-200 shadow-xl shadow-slate-200/50">
        <div class="table-container custom-scrollbar rounded-[1.8rem]">
            <table class="w-full text-left">
                @php
                    $currentSort = $sortBy ?? 'created_at';
                    $currentDir = $sortDir ?? 'desc';
                    $sortUrl = function($field) use ($currentSort, $currentDir) {
                        $newDir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
                        return request()->fullUrlWithQuery(['sort' => $field, 'dir' => $newDir]);
                    };
                    $sortIcon = function($field) use ($currentSort, $currentDir) {
                        if ($currentSort !== $field) return 'ri-arrow-up-down-line';
                        return $currentDir === 'asc' ? 'ri-sort-asc' : 'ri-sort-desc';
                    };
                @endphp
                <thead class="text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-extrabold sticky-th header-solid">
                            <a href="{{ $sortUrl('created_at') }}" class="flex items-center gap-1 hover:text-blue-200 transition">
                                Waktu <i class="{{ $sortIcon('created_at') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-4 font-extrabold sticky-th header-solid">User</th>
                        <th class="px-6 py-4 font-extrabold sticky-th header-solid {{ $currentSort === 'action' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('action') }}" class="flex items-center gap-1 hover:text-blue-200 transition">
                                Event <i class="{{ $sortIcon('action') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-4 font-extrabold sticky-th header-solid {{ $currentSort === 'ip_address' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('ip_address') }}" class="flex items-center gap-1 hover:text-blue-200 transition">
                                IP Address <i class="{{ $sortIcon('ip_address') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-4 font-extrabold sticky-th header-solid">Deskripsi</th>
                        <th class="px-6 py-4 font-extrabold text-center sticky-th header-solid {{ $currentSort === 'type' ? 'sort-active' : '' }}">
                            <a href="{{ $sortUrl('type') }}" class="flex items-center gap-1 justify-center hover:text-blue-200 transition">
                                Type <i class="{{ $sortIcon('type') }} sort-icon ml-1"></i>
                            </a>
                        </th>
                        <th class="px-6 py-4 font-extrabold text-right sticky-th header-solid">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($logs as $log)
                    <tr class="hover:bg-blue-50/50 transition group">
                        <td class="px-6 py-4">
                            <div class="text-xs">
                                <div class="font-bold text-slate-700">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-slate-500 flex items-center gap-1"><i class="ri-time-line"></i> {{ $log->created_at->format('H:i:s') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user->name) }}&background=0284c7&color=fff&size=32"
                                         class="w-8 h-8 rounded-full shadow-sm" alt="">
                                    <div>
                                        <div class="font-bold text-slate-800 text-xs">{{ $log->user->name }}</div>
                                        @php
                                            $roleBadge = match($log->user->role ?? '') {
                                                'admin' => 'bg-red-100 text-red-600',
                                                'reseller_vip' => 'bg-purple-100 text-purple-600',
                                                'reseller' => 'bg-blue-100 text-blue-600',
                                                default => 'bg-slate-100 text-slate-600',
                                            };
                                        @endphp
                                        <span class="{{ $roleBadge }} text-[10px] font-bold px-1.5 py-0.5 rounded">{{ ucfirst($log->user->role ?? 'user') }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @php
                                    $actionColor = match(strtolower($log->action ?? '')) {
                                        'create', 'add' => 'bg-emerald-100 text-emerald-600',
                                        'update', 'edit' => 'bg-blue-100 text-blue-600',
                                        'delete', 'remove' => 'bg-red-100 text-red-600',
                                        'login' => 'bg-purple-100 text-purple-600',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="w-8 h-8 rounded-lg {{ $actionColor }} flex items-center justify-center text-sm">
                                    <i class="{{ $log->action_icon ?? 'ri-flashlight-line' }}"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-slate-700 text-xs uppercase">{{ $log->action }}</span>
                                    <span class="block text-[10px] text-slate-400">{{ $log->module }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <code class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-600 select-all">{{ $log->ip_address }}</code>
                                <button onclick="navigator.clipboard.writeText('{{ $log->ip_address }}'); this.innerHTML='<i class=\'ri-check-line text-green-500\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'ri-file-copy-line\'></i>',1000)" class="text-slate-300 hover:text-brand-600 transition opacity-0 group-hover:opacity-100" title="Copy IP">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-slate-600 text-xs max-w-xs truncate" title="{{ $log->description }}">{{ $log->description }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $typeColors = [
                                    'info' => 'bg-blue-100 text-blue-600',
                                    'success' => 'bg-emerald-100 text-emerald-600',
                                    'warning' => 'bg-amber-100 text-amber-600',
                                    'error' => 'bg-red-100 text-red-600',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $typeColors[$log->type] ?? $typeColors['info'] }}">
                                {{ ucfirst($log->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Detail">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <form action="{{ route('admin.activity-logs.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition shadow-sm flex items-center justify-center" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-history-line text-slate-400 text-3xl"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 mb-1">Belum ada log</h3>
                            <p class="text-slate-500 text-sm">Aktivitas akan tercatat di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <div class="text-xs font-bold text-slate-500">
                Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $logs->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ $logs->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-skip-back-line"></i>
                </a>
                <a href="{{ $logs->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ $logs->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
                <div class="flex items-center gap-2 mx-2">
                    <span class="text-xs font-bold text-slate-500">Page</span>
                    <span class="w-12 h-8 flex items-center justify-center text-xs font-bold border border-slate-200 rounded-lg bg-white">{{ $logs->currentPage() }}</span>
                    <span class="text-xs font-bold text-slate-500">of {{ $logs->lastPage() }}</span>
                </div>
                <a href="{{ $logs->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ !$logs->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
                <a href="{{ $logs->url($logs->lastPage()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-600 hover:text-white {{ !$logs->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }} transition shadow-sm">
                    <i class="ri-skip-forward-line"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Clear All Confirmation Modal -->
<div id="clear-modal" class="fixed inset-0 z-[70] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-filter backdrop-blur-sm" onclick="document.getElementById('clear-modal').classList.add('hidden')"></div>
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm relative overflow-hidden animate-popup p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500">
            <i class="ri-delete-bin-2-fill text-3xl"></i>
        </div>
        <h3 class="font-bold text-xl text-slate-800 mb-2">Hapus Semua Log?</h3>
        <p class="text-sm text-slate-500 mb-4">Semua log aktivitas akan dihapus secara permanen. Ketik <strong>DELETE</strong> untuk konfirmasi.</p>
        <input type="text" id="clear-confirm-input" placeholder="Ketik DELETE" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-bold text-center mb-4 focus:outline-none focus:border-red-500">
        <div class="flex gap-3">
            <button onclick="document.getElementById('clear-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
            <form action="{{ route('admin.activity-logs.clear') }}" method="POST" class="flex-1" id="clear-form">
                @csrf
                @method('DELETE')
                <input type="hidden" name="confirm" value="DELETE">
                <button type="submit" onclick="if(document.getElementById('clear-confirm-input').value !== 'DELETE'){event.preventDefault();document.getElementById('clear-confirm-input').focus();}" class="w-full py-2.5 rounded-xl font-bold text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/30 transition">Hapus Semua</button>
            </form>
        </div>
    </div>
</div>
@endsection
