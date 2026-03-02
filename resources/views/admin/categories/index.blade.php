@extends('layouts.admin')

@section('title', 'Kelola Kategori')
@section('page-title', 'Kategori Produk')
@section('page-description', 'Atur urutan dan status aktif kategori.')

@push('styles')
<style>
    .table-container { max-height: 65vh; overflow-y: auto; }
    .sticky-th { position: sticky; top: 0; z-index: 20; }
    .header-solid { background: #0033AA; color: white; }
    @keyframes popup { 0% { opacity: 0; transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
    .animate-popup { animation: popup 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div></div>
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.create') }}" class="px-5 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-xs shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                <i class="ri-add-line text-lg"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-2 rounded-[1.5rem] shadow-sm border border-slate-200 flex flex-col md:flex-row gap-2 items-center">
        <div class="relative w-full md:w-64 group">
            <i class="ri-search-2-line absolute left-4 top-3 text-slate-400 group-focus-within:text-brand-600 transition-colors"></i>
            <input type="text" id="search-input" value="{{ request('search') }}" placeholder="Cari Kategori..."
                   class="w-full bg-slate-50 border border-transparent pl-11 pr-4 py-2.5 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition-all"
                   onkeydown="if(event.key==='Enter'){updateFilters()}">
        </div>

        <div class="flex gap-2 w-full md:w-auto">
            <select id="status-filter" onchange="updateFilters()" class="bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl px-4 py-2.5 cursor-pointer focus:outline-none focus:border-brand-500 min-w-[120px]">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center gap-1">
                <i class="ri-close-line"></i> Reset
            </a>
            @endif
        </div>
    </div>

    <!-- Categories Table -->
    <div class="glass-panel rounded-[2rem] p-1 shadow-xl shadow-slate-200/50 overflow-hidden">
        @if($categories->count() > 0)
        <div class="table-container custom-scrollbar rounded-[1.8rem]">
            <table class="w-full text-left border-collapse">
                <thead class="text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-5 font-extrabold w-20 text-center sticky-th header-solid">
                            <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort' => 'sort_order', 'dir' => request('sort') === 'sort_order' && request('dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center gap-1 hover:text-blue-200 transition">
                                Posisi
                                @if(request('sort') === 'sort_order')
                                    <i class="ri-sort-{{ request('dir') === 'desc' ? 'desc' : 'asc' }}"></i>
                                @else
                                    <i class="ri-sort-asc opacity-60"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold sticky-th header-solid">
                            <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort' => 'name', 'dir' => request('sort') === 'name' && request('dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-blue-200 transition">
                                Nama Kategori
                                @if(request('sort') === 'name')
                                    <i class="ri-sort-{{ request('dir') === 'desc' ? 'desc' : 'asc' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold sticky-th header-solid">Slug (Quick Edit)</th>
                        <th class="px-6 py-5 font-extrabold text-center sticky-th header-solid">Total Produk</th>
                        <th class="px-6 py-5 font-extrabold text-center sticky-th header-solid">
                            <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort' => 'is_active', 'dir' => request('sort') === 'is_active' && request('dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center gap-1 hover:text-blue-200 transition">
                                Status
                                @if(request('sort') === 'is_active')
                                    <i class="ri-sort-{{ request('dir') === 'desc' ? 'desc' : 'asc' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-5 font-extrabold text-right sticky-th header-solid">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-slate-600 divide-y divide-slate-50">
                    @foreach($categories as $category)
                    <tr class="hover:bg-slate-50 transition-colors group" data-id="{{ $category->id }}">
                        <!-- Position (Inline Edit) -->
                        <td class="px-6 py-4">
                            <input type="number"
                                   value="{{ $category->sort_order }}"
                                   data-field="sort_order"
                                   data-original="{{ $category->sort_order }}"
                                   onchange="updateInline({{ $category->id }}, 'sort_order', this.value, this)"
                                   class="w-14 h-10 bg-slate-100 text-center font-bold rounded-lg border border-transparent focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition"
                                   min="0">
                        </td>

                        <!-- Name with Icon -->
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="flex items-center gap-3 cursor-pointer group/name">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-white border border-slate-200 flex items-center justify-center text-brand-600 shadow-sm text-xl group-hover/name:scale-110 transition-transform overflow-hidden">
                                    @if($category->icon && str_starts_with($category->icon, 'ri-'))
                                        <i class="{{ $category->icon }}"></i>
                                    @elseif($category->icon)
                                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ri-layout-grid-fill"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 group-hover/name:text-brand-600 transition-colors flex items-center gap-2">
                                        {{ $category->name }}
                                        <i class="ri-pencil-fill opacity-0 group-hover/name:opacity-100 text-xs text-brand-400"></i>
                                    </div>
                                    @if($category->description)
                                        <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($category->description, 40) }}</p>
                                    @endif
                                </div>
                            </a>
                        </td>

                        <!-- Slug (Inline Edit) -->
                        <td class="px-6 py-4">
                            <div class="relative group/slug">
                                <div class="absolute inset-y-0 left-2 flex items-center pointer-events-none text-slate-400">
                                    <i class="ri-link"></i>
                                </div>
                                <input type="text"
                                       value="{{ $category->slug }}"
                                       data-field="slug"
                                       data-original="{{ $category->slug }}"
                                       onchange="updateInline({{ $category->id }}, 'slug', this.value, this)"
                                       class="w-full pl-7 pr-2 py-1.5 bg-transparent border-b border-dashed border-slate-300 hover:border-brand-400 focus:border-brand-600 focus:bg-white focus:border-solid outline-none text-xs font-mono text-slate-600 transition-all rounded-t-md"
                                       placeholder="url-slug">
                            </div>
                        </td>

                        <!-- Products Count -->
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-50 text-brand-700 px-3 py-1 rounded-lg text-xs font-bold border border-blue-100">
                                {{ $category->products_count }} Item
                            </span>
                        </td>

                        <!-- Status Toggle -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center">
                                <label class="relative inline-flex items-center cursor-pointer group/toggle" title="Klik untuk Ubah Status">
                                    <input type="checkbox"
                                           {{ $category->is_active ? 'checked' : '' }}
                                           onchange="toggleStatus({{ $category->id }}, this)"
                                           class="sr-only peer">
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500 shadow-inner"></div>
                                    <span class="absolute text-[8px] font-black text-white left-2 opacity-0 peer-checked:opacity-100 transition-opacity uppercase tracking-wider">ON</span>
                                    <span class="absolute text-[8px] font-black text-slate-400 right-2 opacity-100 peer-checked:opacity-0 transition-opacity uppercase tracking-wider">OFF</span>
                                </label>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 hover:bg-brand-600 hover:text-white transition flex items-center justify-center" title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                @if($category->products_count === 0)
                                <button onclick="confirmDeleteCat({{ $category->id }}, '{{ addslashes($category->name) }}')" class="w-8 h-8 rounded-lg bg-slate-50 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                                @else
                                <button type="button" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed flex items-center justify-center" title="Tidak bisa hapus, ada {{ $category->products_count }} produk">
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
        <div class="bg-white border-t border-slate-100 p-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <p class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                    Total: <span class="text-brand-600">{{ $categories->total() }}</span>
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Tampilkan</span>
                    <select id="per-page" onchange="updateFilters()" class="bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-lg px-2 py-1.5 focus:outline-none cursor-pointer">
                        @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap justify-center">
                {{-- First & Prev --}}
                <a href="{{ $categories->url(1) }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ $categories->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}" title="First Page">
                    <i class="ri-skip-back-line"></i>
                </a>
                <a href="{{ $categories->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ $categories->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}" title="Previous">
                    <i class="ri-arrow-left-s-line"></i>
                </a>

                <span class="text-xs font-bold text-slate-700 mx-2">Page {{ $categories->currentPage() }} / {{ $categories->lastPage() }}</span>

                {{-- Next & Last --}}
                <a href="{{ $categories->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ !$categories->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}" title="Next">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
                <a href="{{ $categories->url($categories->lastPage()) }}" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 {{ !$categories->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}" title="Last Page">
                    <i class="ri-skip-forward-line"></i>
                </a>

                {{-- Jump to page --}}
                <div class="flex items-center gap-1 ml-2 pl-2 border-l border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Go to</span>
                    <input type="number" id="jump-page" min="1" max="{{ $categories->lastPage() }}"
                           class="w-12 h-8 rounded-lg border border-slate-200 text-center text-xs font-bold text-slate-700 focus:outline-none focus:border-brand-500"
                           onkeydown="if(event.key==='Enter'){jumpToPage()}">
                    <button onclick="jumpToPage()" class="h-8 px-2 rounded-lg bg-brand-50 text-brand-600 text-[10px] font-bold hover:bg-brand-600 hover:text-white transition">Go</button>
                </div>
            </div>
        </div>
        @else
        <div class="p-16 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-folder-open-line text-slate-400 text-4xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 mb-2">Belum ada kategori</h3>
            <p class="text-slate-500 text-sm mb-6">Silakan tambah kategori untuk produk.</p>
            <a href="{{ route('admin.categories.create') }}" class="px-6 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95 inline-flex items-center">
                <i class="ri-add-line mr-2"></i> Tambah Kategori
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-[70] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-filter backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm relative overflow-hidden animate-popup p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500">
            <i class="ri-delete-bin-2-fill text-3xl"></i>
        </div>
        <h3 class="font-bold text-xl text-slate-800 mb-2">Hapus Kategori?</h3>
        <p class="text-sm text-slate-500 mb-6">Kategori <strong id="delete-cat-name"></strong> akan dihapus secara permanen.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
            <form id="delete-cat-form" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/30 transition">Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed inset-0 z-[100] flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-300">
    <div class="bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[300px] pointer-events-auto border border-white/10">
        <div id="toast-icon" class="w-10 h-10 rounded-full flex items-center justify-center text-xl bg-green-500">
            <i class="ri-checkbox-circle-fill"></i>
        </div>
        <div>
            <h4 id="toast-title" class="font-bold text-base">Berhasil</h4>
            <p id="toast-message" class="text-xs text-slate-300">Perubahan tersimpan</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toast-icon');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');

    toastTitle.textContent = title;
    toastMessage.textContent = message;

    if (type === 'success') {
        toastIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-xl bg-green-500';
        toastIcon.innerHTML = '<i class="ri-checkbox-circle-fill"></i>';
    } else {
        toastIcon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-xl bg-red-500';
        toastIcon.innerHTML = '<i class="ri-error-warning-fill"></i>';
    }

    toast.classList.remove('opacity-0');
    toast.classList.add('opacity-100');

    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
    }, 3000);
}

function updateFilters() {
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    const perPage = document.getElementById('per-page').value;

    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    url.searchParams.set('status', status);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to page 1

    // Remove empty params
    if (!search) url.searchParams.delete('search');
    if (!status) url.searchParams.delete('status');

    window.location.href = url.toString();
}

function jumpToPage() {
    const page = document.getElementById('jump-page').value;
    if (page >= 1 && page <= {{ $categories->lastPage() }}) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    } else {
        showToast('Error', 'Halaman tidak valid', 'error');
    }
}

function toggleStatus(categoryId, checkbox) {
    const originalState = !checkbox.checked;

    fetch(`{{ url('admin/categories') }}/${categoryId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Berhasil', data.message, 'success');
        } else {
            checkbox.checked = originalState;
            showToast('Error', data.message || 'Gagal mengubah status', 'error');
        }
    })
    .catch(error => {
        checkbox.checked = originalState;
        showToast('Error', 'Terjadi kesalahan', 'error');
        console.error('Error:', error);
    });
}

function confirmDeleteCat(id, name) {
    document.getElementById('delete-cat-name').textContent = name;
    document.getElementById('delete-cat-form').action = '{{ url("admin/categories") }}/' + id;
    document.getElementById('delete-modal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});

function updateInline(categoryId, field, value, input) {
    const originalValue = input.dataset.original;

    fetch(`{{ url('admin/categories') }}/${categoryId}/inline-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ field, value })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.dataset.original = data.value;
            input.value = data.value;
            showToast('Berhasil', data.message, 'success');
        } else {
            input.value = originalValue;
            showToast('Error', data.message || 'Gagal mengupdate', 'error');
        }
    })
    .catch(error => {
        input.value = originalValue;
        showToast('Error', 'Terjadi kesalahan', 'error');
        console.error('Error:', error);
    });
}
</script>
@endpush
@endsection
