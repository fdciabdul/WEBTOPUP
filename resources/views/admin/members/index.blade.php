@extends('layouts.admin')

@section('title', 'Kelola Member')
@section('page-title', 'Kelola Member')

@push('styles')
<style>
    .table-container { max-height: 65vh; overflow-y: auto; }
    .table-container::-webkit-scrollbar { width: 6px; height: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .sticky-th { position: sticky; top: 0; z-index: 10; }
    .header-solid { background: #0033AA; color: white; }
    .th-sortable { cursor: pointer; user-select: none; transition: background 0.2s; }
    .th-sortable:hover { background: rgba(255,255,255,0.15); }
    .sort-icon { display: inline-block; margin-left: 4px; font-size: 11px; opacity: 0.8; }
    .copy-hover { position: relative; cursor: pointer; transition: all 0.2s; }
    .copy-hover:hover { color: #0033AA; }
    .copy-hover:active { transform: scale(0.95); }
    .animate-popup { animation: popupIn 0.25s cubic-bezier(0.175,0.885,0.32,1.275) forwards; }
    @keyframes popupIn { from { opacity:0; transform:scale(0.9) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .pagination-btn {
        width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
        border-radius: 0.6rem; border: 1px solid #E2E8F0; color: #64748B; background: white;
        transition: all 0.2s; font-size: 1.1rem;
    }
    .pagination-btn:hover:not(:disabled) { background: #F1F5F9; color: #0F172A; border-color: #CBD5E1; }
    .pagination-btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
@endpush

@section('content')
@php
    $sortBy = $sortBy ?? 'created_at';
    $sortDir = $sortDir ?? 'desc';
    $timeFilter = $timeFilter ?? 'all';
    $perPage = $perPage ?? 25;

    $sortUrl = function($col) use ($sortBy, $sortDir) {
        $newDir = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $newDir]);
    };
    $sortIcon = function($col) use ($sortBy, $sortDir) {
        if ($sortBy !== $col) return '<i class="ri-expand-up-down-fill sort-icon opacity-50"></i>';
        return $sortDir === 'asc'
            ? '<i class="ri-arrow-up-s-fill sort-icon"></i>'
            : '<i class="ri-arrow-down-s-fill sort-icon"></i>';
    };
@endphp

<div class="space-y-6">
    <!-- Filters -->
    <div class="glass-panel rounded-[2rem] p-4 card-anim animate-slide-up-fade">
        <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-col md:flex-row gap-2">
            <div class="relative flex-1 group">
                <i class="ri-search-2-line absolute left-4 top-3 text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                <input type="text" name="search" placeholder="Cari Nama, Email, ID, atau No HP..."
                       value="{{ request('search') }}" class="w-full bg-slate-50 border-none pl-11 pr-4 py-2.5 rounded-xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:bg-white transition-all">
            </div>

            <div class="flex gap-2 overflow-x-auto pb-1 md:pb-0">
                <select name="level" class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-xl px-4 py-2.5 cursor-pointer focus:outline-none min-w-[120px]">
                    <option value="">Semua Level</option>
                    <option value="visitor" {{ request('level') == 'visitor' ? 'selected' : '' }}>Visitor</option>
                    <option value="reseller" {{ request('level') == 'reseller' ? 'selected' : '' }}>Reseller</option>
                    <option value="reseller_vip" {{ request('level') == 'reseller_vip' ? 'selected' : '' }}>Reseller VIP</option>
                    <option value="reseller_vvip" {{ request('level') == 'reseller_vvip' ? 'selected' : '' }}>Reseller VVIP</option>
                </select>

                <select name="time" class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-xl px-4 py-2.5 cursor-pointer focus:outline-none min-w-[120px]">
                    <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $timeFilter == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $timeFilter == 'month' ? 'selected' : '' }}>This Month</option>
                </select>

                <select name="is_active" class="bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-xl px-4 py-2.5 cursor-pointer focus:outline-none min-w-[120px]">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-brand-500/30 transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap">
                    <i class="ri-search-line"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'level', 'is_active', 'time']))
                    <a href="{{ route('admin.members.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl font-bold text-xs transition-all flex items-center gap-1 whitespace-nowrap">
                        <i class="ri-close-line"></i> Reset
                    </a>
                @endif
            </div>

            <a href="{{ route('admin.members.create') }}" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-brand-500/30 transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap">
                <i class="ri-user-add-line text-lg"></i> Tambah Member
            </a>
        </form>
    </div>

    <!-- Members Table -->
    <div class="glass-panel rounded-[2rem] p-1 card-anim animate-slide-up-fade">
        @if($members->count() > 0)
        <div class="table-container custom-scrollbar rounded-[1.8rem]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] uppercase font-extrabold tracking-wider">
                        <th class="sticky-th header-solid px-5 py-4 rounded-tl-[1.8rem]">
                            <a href="{{ $sortUrl('name') }}" class="th-sortable flex items-center gap-1 text-white hover:text-white/80">
                                Member Info {!! $sortIcon('name') !!}
                            </a>
                        </th>
                        <th class="sticky-th header-solid px-5 py-4">Kontak</th>
                        <th class="sticky-th header-solid px-5 py-4">
                            <a href="{{ $sortUrl('level') }}" class="th-sortable flex items-center gap-1 text-white hover:text-white/80">
                                Level {!! $sortIcon('level') !!}
                            </a>
                        </th>
                        <th class="sticky-th header-solid px-5 py-4 text-right">
                            <a href="{{ $sortUrl('balance') }}" class="th-sortable flex items-center justify-end gap-1 text-white hover:text-white/80">
                                Sisa Saldo {!! $sortIcon('balance') !!}
                            </a>
                        </th>
                        <th class="sticky-th header-solid px-5 py-4 text-right">
                            <a href="{{ $sortUrl('transactions_sum_total_amount') }}" class="th-sortable flex items-center justify-end gap-1 text-white hover:text-white/80">
                                Pemakaian {!! $sortIcon('transactions_sum_total_amount') !!}
                            </a>
                        </th>
                        <th class="sticky-th header-solid px-5 py-4 text-center">
                            <a href="{{ $sortUrl('transactions_count') }}" class="th-sortable flex items-center justify-center gap-1 text-white hover:text-white/80">
                                Total Trx {!! $sortIcon('transactions_count') !!}
                            </a>
                        </th>
                        <th class="sticky-th header-solid px-5 py-4 text-right rounded-tr-[1.8rem]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-50">
                    @foreach($members as $member)
                    <tr class="hover:bg-blue-50/30 transition-colors duration-200 bg-white group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-600 font-bold flex items-center justify-center text-sm shadow-inner border border-white">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.members.edit', $member) }}" class="font-bold text-slate-800 hover:text-brand-600 hover:underline decoration-2 underline-offset-2 transition-colors">{{ $member->name }}</a>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">ID: {{ $member->id }} &bull; {{ $member->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-xs copy-hover w-fit" onclick="copyText('{{ $member->phone }}', 'Nomor HP')" title="Klik untuk copy">
                                    <i class="ri-whatsapp-fill text-green-500 text-sm"></i> <span class="font-mono text-slate-700 font-bold">{{ $member->phone }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs copy-hover w-fit" onclick="copyText('{{ $member->email }}', 'Email')" title="Klik untuk copy">
                                    <i class="ri-mail-fill text-blue-500 text-sm"></i> <span class="text-slate-500">{{ $member->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $levelBadge = match($member->level) {
                                    'reseller_vvip' => 'bg-gradient-to-r from-red-500 to-pink-500 text-white shadow-red-500/30',
                                    'reseller_vip' => 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-amber-500/30',
                                    'reseller' => 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-blue-500/30',
                                    default => 'bg-slate-200 text-slate-600'
                                };
                            @endphp
                            <span class="{{ $levelBadge }} px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-md inline-block">
                                {{ ucfirst(str_replace('_', ' ', $member->level)) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-bold text-slate-800 copy-hover" onclick="copyText('Rp {{ number_format($member->balance, 0, ',', '.') }}', 'Saldo')">
                                Rp {{ number_format($member->balance, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-bold text-red-500 copy-hover" onclick="copyText('Rp {{ number_format($member->transactions_sum_total_amount ?? 0, 0, ',', '.') }}', 'Total Pemakaian')">
                                Rp {{ number_format($member->transactions_sum_total_amount ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-full inline-block border border-slate-200">{{ $member->transactions_count }} Order</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.members.show', $member) }}" class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition border border-teal-200 flex items-center justify-center shadow-sm" title="Detail & Riwayat">
                                    <i class="ri-time-line"></i>
                                </a>
                                <button onclick="openBalanceModal({{ $member->id }}, '{{ addslashes($member->name) }}', {{ $member->balance }})" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition border border-green-200 flex items-center justify-center shadow-sm" title="Kelola Saldo">
                                    <i class="ri-wallet-3-fill"></i>
                                </button>
                                <a href="{{ route('admin.members.edit', $member) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition border border-indigo-200 flex items-center justify-center shadow-sm" title="Edit Data">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                @if(!$member->is_admin)
                                <button onclick="confirmDeleteMember({{ $member->id }}, '{{ addslashes($member->name) }}')" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition border border-red-200 flex items-center justify-center shadow-sm" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white/50 border-t border-slate-100 p-4 flex flex-col md:flex-row justify-between items-center gap-4 rounded-b-[1.8rem]">
            <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-start">
                <span class="text-xs font-bold text-slate-500">Tampilkan</span>
                <form method="GET" action="{{ route('admin.members.index') }}" class="inline">
                    @foreach(request()->except(['per_page', 'page']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-lg px-2 py-2 cursor-pointer focus:outline-none hover:bg-slate-100 transition">
                        @foreach([10, 25, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }} Data</option>
                        @endforeach
                    </select>
                </form>
                <span class="text-xs font-bold text-slate-400 border-l border-slate-200 pl-3 ml-1">
                    Total: <span class="text-brand-600">{{ $members->total() }}</span> Member
                </span>
            </div>

            <div class="flex items-center gap-2">
                {{-- Skip to first --}}
                @if($members->currentPage() > 1)
                    <a href="{{ $members->url(1) }}" class="pagination-btn" title="Halaman Pertama"><i class="ri-skip-back-mini-line"></i></a>
                    <a href="{{ $members->previousPageUrl() }}" class="pagination-btn" title="Sebelumnya"><i class="ri-arrow-left-s-line"></i></a>
                @else
                    <button disabled class="pagination-btn"><i class="ri-skip-back-mini-line"></i></button>
                    <button disabled class="pagination-btn"><i class="ri-arrow-left-s-line"></i></button>
                @endif

                <div class="flex items-center gap-2 mx-1 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                    <span class="text-xs font-bold text-slate-700">{{ $members->currentPage() }}</span>
                    <span class="text-xs font-bold text-slate-400">/ {{ $members->lastPage() }}</span>
                </div>

                @if($members->hasMorePages())
                    <a href="{{ $members->nextPageUrl() }}" class="pagination-btn" title="Selanjutnya"><i class="ri-arrow-right-s-line"></i></a>
                    <a href="{{ $members->url($members->lastPage()) }}" class="pagination-btn" title="Halaman Terakhir"><i class="ri-skip-forward-mini-line"></i></a>
                @else
                    <button disabled class="pagination-btn"><i class="ri-arrow-right-s-line"></i></button>
                    <button disabled class="pagination-btn"><i class="ri-skip-forward-mini-line"></i></button>
                @endif
            </div>
        </div>
        @else
        <div class="p-16 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-user-3-line text-slate-400 text-4xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 mb-2">Belum ada member</h3>
            <p class="text-slate-500 text-sm">Member akan muncul di sini</p>
        </div>
        @endif
    </div>
</div>

<!-- Balance Modal (dark header like template) -->
<div id="balanceModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeBalanceModal()"></div>
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg relative overflow-hidden z-10 animate-popup">
        <!-- Dark header -->
        <div class="bg-slate-900 p-6 pb-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500 rounded-full blur-[60px] opacity-40"></div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <h3 class="font-black text-xl">Kelola Saldo</h3>
                    <p class="text-xs text-slate-400 mt-1">Topup, Tarik, atau Atur Saldo Manual</p>
                </div>
                <button onclick="closeBalanceModal()" class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-red-500 transition"><i class="ri-close-line"></i></button>
            </div>
            <div class="mt-6 bg-white/10 rounded-xl p-4 border border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-500 text-white font-bold flex items-center justify-center border border-white/20" id="balanceAvatar">-</div>
                    <div>
                        <div class="font-bold text-white text-sm" id="balanceMemberName">-</div>
                        <div class="text-[10px] text-indigo-200" id="balanceMemberId"></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-indigo-200 uppercase">Saldo Saat Ini</div>
                    <div class="font-black text-xl text-white">Rp <span id="balanceCurrentDisplay">0</span></div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Tab buttons -->
            <div class="flex p-1 bg-slate-100 rounded-xl mb-6">
                <button onclick="setBalanceType('add')" id="btnAdd" class="flex-1 py-2 text-xs font-extrabold rounded-lg transition bg-white shadow text-indigo-600">TOPUP</button>
                <button onclick="setBalanceType('deduct')" id="btnDeduct" class="flex-1 py-2 text-xs font-extrabold rounded-lg transition text-slate-500">TARIK</button>
            </div>

            <form id="balanceForm" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-4 text-xl font-black text-slate-800 focus:outline-none focus:border-indigo-500 transition placeholder:text-slate-300" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Catatan (Opsional)</label>
                        <input type="text" name="description" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition" placeholder="Contoh: Bonus Event / Refund">
                    </div>
                    <button type="submit" id="balanceSubmitBtn" class="w-full py-4 rounded-xl font-bold text-white shadow-lg transition transform active:scale-[0.98] bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/30">
                        + Tambah Saldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="bg-white rounded-3xl p-8 w-full max-w-sm shadow-2xl animate-popup text-center relative overflow-hidden z-10">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-rose-500"></div>
        <div class="w-20 h-20 rounded-full bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-6 text-4xl shadow-lg">
            <i class="ri-error-warning-fill"></i>
        </div>
        <h3 class="font-black text-2xl text-slate-800 mb-2 tracking-tight">Hapus Member?</h3>
        <p class="text-sm text-slate-500 mb-8 font-medium px-2 leading-relaxed">
            Member <strong id="deleteTargetName" class="text-red-600"></strong> akan dihapus permanen. Data tidak dapat dikembalikan.
        </p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-3.5 font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
            <form id="delete-member-form" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-3.5 font-bold text-white rounded-xl bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/30 transition active:scale-[0.98] flex items-center justify-center gap-2">
                    <i class="ri-check-line text-lg"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentMemberId = null;

function openBalanceModal(id, name, balance) {
    currentMemberId = id;
    document.getElementById('balanceMemberName').textContent = name;
    document.getElementById('balanceAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('balanceMemberId').textContent = 'ID: ' + id;
    document.getElementById('balanceCurrentDisplay').textContent = new Intl.NumberFormat('id-ID').format(balance);
    setBalanceType('add');
    document.getElementById('balanceModal').classList.remove('hidden');
    document.getElementById('balanceModal').classList.add('flex');
}

function closeBalanceModal() {
    document.getElementById('balanceModal').classList.add('hidden');
    document.getElementById('balanceModal').classList.remove('flex');
}

function setBalanceType(type) {
    const form = document.getElementById('balanceForm');
    const btnAdd = document.getElementById('btnAdd');
    const btnDeduct = document.getElementById('btnDeduct');
    const submitBtn = document.getElementById('balanceSubmitBtn');

    if (type === 'add') {
        form.action = `{{ url("admin/members") }}/${currentMemberId}/add-balance`;
        btnAdd.className = 'flex-1 py-2 text-xs font-extrabold rounded-lg transition bg-white shadow text-indigo-600';
        btnDeduct.className = 'flex-1 py-2 text-xs font-extrabold rounded-lg transition text-slate-500';
        submitBtn.textContent = '+ Tambah Saldo';
        submitBtn.className = 'w-full py-4 rounded-xl font-bold text-white shadow-lg transition transform active:scale-[0.98] bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/30';
    } else {
        form.action = `{{ url("admin/members") }}/${currentMemberId}/deduct-balance`;
        btnAdd.className = 'flex-1 py-2 text-xs font-extrabold rounded-lg transition text-slate-500';
        btnDeduct.className = 'flex-1 py-2 text-xs font-extrabold rounded-lg transition bg-white shadow text-indigo-600';
        submitBtn.textContent = '- Tarik Saldo';
        submitBtn.className = 'w-full py-4 rounded-xl font-bold text-white shadow-lg transition transform active:scale-[0.98] bg-red-500 hover:bg-red-600 shadow-red-500/30';
    }
}

function confirmDeleteMember(id, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('delete-member-form').action = `{{ url("admin/members") }}/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function copyText(text, label) {
    navigator.clipboard.writeText(text).then(() => {
        // Quick toast feedback
        const toast = document.createElement('div');
        toast.className = 'fixed top-6 right-6 z-[200] bg-white/95 backdrop-blur-xl border-l-4 border-green-500 py-3 pl-4 pr-6 rounded-xl shadow-2xl flex items-center gap-3 animate-popup';
        toast.innerHTML = `<div class="w-7 h-7 rounded-full bg-gradient-to-br from-green-400 to-green-600 text-white flex items-center justify-center text-xs"><i class="ri-checkbox-circle-fill"></i></div><div><div class="font-extrabold text-slate-800 text-xs">Disalin!</div><div class="text-[10px] text-slate-500">${label} berhasil disalin</div></div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>
@endpush
@endsection
