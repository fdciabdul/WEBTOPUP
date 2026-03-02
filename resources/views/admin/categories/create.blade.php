@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}" class="w-10 h-10 rounded-xl bg-white/50 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-500 transition-all">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">Tambah Kategori</h2>
            <p class="text-sm text-slate-500">Buat kategori produk baru</p>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="glass-panel rounded-[2rem] p-8 card-anim animate-slide-up-fade space-y-8">
            <!-- Basic Info -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="ri-information-line text-brand-600"></i> Informasi Dasar
                </h3>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm"
                               placeholder="Contoh: Mobile Legends" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm"
                               placeholder="akan otomatis terisi jika dikosongkan">
                        <p class="text-xs text-slate-400 mt-2">Biarkan kosong untuk generate otomatis dari nama</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm resize-none"
                                  placeholder="Deskripsi kategori...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Icon/Gambar</label>

                        <!-- Icon Type Selector -->
                        <div class="flex gap-3 mb-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="icon_type" value="remix" class="peer hidden" checked onchange="toggleIconType('remix')">
                                <div class="px-4 py-2 rounded-xl border-2 border-slate-200 text-center font-bold text-xs text-slate-600 peer-checked:bg-brand-600 peer-checked:border-brand-600 peer-checked:text-white transition-all">
                                    <i class="ri-remixicon-line mr-1"></i> Remix Icon
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="icon_type" value="image" class="peer hidden" onchange="toggleIconType('image')">
                                <div class="px-4 py-2 rounded-xl border-2 border-slate-200 text-center font-bold text-xs text-slate-600 peer-checked:bg-brand-600 peer-checked:border-brand-600 peer-checked:text-white transition-all">
                                    <i class="ri-image-line mr-1"></i> Upload Gambar
                                </div>
                            </label>
                        </div>

                        <!-- Remix Icon Input -->
                        <div id="remix-icon-section">
                            <div class="flex gap-3 items-end">
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-slate-100 to-white border border-slate-200 flex items-center justify-center text-brand-600 text-2xl shadow-sm" id="icon-preview">
                                    <i id="icon-preview-i" class="ri-layout-grid-fill"></i>
                                </div>
                                <div class="flex-1">
                                    <input type="text" name="icon" id="icon-class-input" value="{{ old('icon', 'ri-layout-grid-fill') }}"
                                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm font-mono"
                                           placeholder="ri-gamepad-line" oninput="updateIconPreview(this.value)">
                                    <p class="text-xs text-slate-400 mt-1">Contoh: ri-gamepad-line, ri-apps-line. <a href="https://remixicon.com/" target="_blank" class="text-brand-600 hover:underline">Lihat daftar icon</a></p>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload Section (Hidden by default) -->
                        <div id="image-upload-section" class="hidden">
                            <input type="file" name="icon_file" id="icon-input" accept="image/*" class="hidden" onchange="previewImage(event)">
                            <label for="icon-input" id="upload-label" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-brand-500 hover:bg-brand-50/30 transition-all">
                                <i class="ri-image-add-line text-4xl text-slate-400 mb-3"></i>
                                <span class="text-sm font-medium text-slate-600">Klik untuk upload gambar</span>
                                <span class="text-xs text-slate-400 mt-1">Format: JPG, PNG, GIF (Max: 2MB)</span>
                            </label>
                            <div id="image-preview" class="hidden mt-4">
                                <div class="relative inline-block">
                                    <img id="preview-img" src="" alt="Preview" class="max-w-[150px] max-h-[150px] rounded-xl shadow-lg">
                                    <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-all shadow-lg">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="ri-settings-3-line text-brand-600"></i> Pengaturan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Urutan</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all text-sm">
                        <p class="text-xs text-slate-400 mt-2">Angka kecil = tampil lebih dulu</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                        <div class="flex gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="is_active" value="1" class="peer hidden" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border-2 border-slate-200 text-center font-bold text-sm text-slate-600 peer-checked:bg-gradient-to-r peer-checked:from-brand-600 peer-checked:to-indigo-600 peer-checked:border-brand-600 peer-checked:text-white transition-all">
                                    Aktif
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="is_active" value="0" class="peer hidden" {{ old('is_active') == '0' ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border-2 border-slate-200 text-center font-bold text-sm text-slate-600 peer-checked:bg-slate-600 peer-checked:border-slate-600 peer-checked:text-white transition-all">
                                    Nonaktif
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition-all">
                    <i class="ri-close-line mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 transition-all hover:-translate-y-0.5 active:scale-95">
                    <i class="ri-save-line mr-2"></i> Simpan Kategori
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleIconType(type) {
    const remixSection = document.getElementById('remix-icon-section');
    const imageSection = document.getElementById('image-upload-section');
    const iconClassInput = document.getElementById('icon-class-input');

    if (type === 'remix') {
        remixSection.classList.remove('hidden');
        imageSection.classList.add('hidden');
        iconClassInput.name = 'icon';
    } else {
        remixSection.classList.add('hidden');
        imageSection.classList.remove('hidden');
        iconClassInput.name = '';
    }
}

function updateIconPreview(value) {
    const iconPreview = document.getElementById('icon-preview-i');
    if (value && value.startsWith('ri-')) {
        iconPreview.className = value;
    } else {
        iconPreview.className = 'ri-layout-grid-fill';
    }
}

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-label').classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('icon-input').value = '';
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('upload-label').classList.remove('hidden');
}
</script>
@endpush
@endsection
