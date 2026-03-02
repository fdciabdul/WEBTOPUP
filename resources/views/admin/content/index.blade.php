@extends('layouts.admin')

@section('title', 'Content Management')
@section('page-title', 'Content Management')
@section('page-description', 'Kelola Ulasan, FAQ, File, Media & Informasi')

@push('styles')
<style>
    .header-section {
        color: white;
        border-radius: 1.5rem 1.5rem 0 0;
        padding: 1.25rem 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .header-section::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }
    .grad-blue { background: linear-gradient(135deg, #0033AA 0%, #002288 100%); }
    .grad-orange { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); }
    .grad-teal { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); }
    .grad-purple { background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); }
    .grad-indigo { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); }
    .table-container { max-height: 400px; overflow-y: auto; }
    .badge-member { background: linear-gradient(to bottom, #f8fafc, #e2e8f0); color: #475569; border: 1px solid #cbd5e1; }
    .badge-reseller { background: linear-gradient(to bottom, #dbeafe, #bfdbfe); color: #1e40af; border: 1px solid #93c5fd; }
    .badge-vip { background: linear-gradient(135deg, #f0abfc 0%, #e879f9 100%); color: #fff; border: 1px solid #d946ef; }
    .badge-vvip { background: linear-gradient(135deg, #FDB931 0%, #d4af37 50%, #FFD700 100%); color: #78350f; border: 1px solid #b45309; }

    /* Rich Editor Styles */
    .rich-editor-wrapper { display: flex; flex-direction: column; height: 100%; background: #f1f5f9; position: relative; }
    .rich-toolbar { display: flex; gap: 0.5rem; padding: 0.75rem 1.5rem; background: white; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; align-items: center; position: sticky; top: 0; z-index: 20; box-shadow: 0 2px 10px rgba(0,0,0,0.03); justify-content: center; }
    .rich-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; color: #64748b; background: white; border: 1px solid #e2e8f0; transition: all 0.1s; cursor: pointer; }
    .rich-btn:hover { background: #eff6ff; color: #0033AA; border-color: #bfdbfe; }
    .rich-btn:active { transform: scale(0.95); }
    .rich-content-container { flex-grow: 1; overflow-y: auto; padding: 2rem 1rem; display: flex; justify-content: center; }
    .rich-content {
        width: 100%; max-width: 850px;
        background: white;
        min-height: 70vh;
        padding: 3rem;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        border-radius: 4px;
        outline: none;
        font-size: 1rem;
        color: #1e293b;
        line-height: 1.8;
    }
    .rich-content h1, .rich-content h2 { font-weight: 800; margin-bottom: 1rem; color: #0f172a; }
    .rich-content p { margin-bottom: 1rem; }
    .rich-content img { display: block; margin: 1.5rem auto; border: 2px solid transparent; transition: all 0.2s; cursor: pointer; max-width: 100%; }
    .rich-content img:hover { border-color: #0044CC; box-shadow: 0 0 0 4px rgba(0, 68, 204, 0.2); }
    .rich-content img.selected { border-color: #0044CC; box-shadow: 0 0 0 4px rgba(0, 68, 204, 0.3); }
    .rich-content ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
    .rich-content ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
    .rich-content blockquote { border-left: 4px solid #0044CC; padding-left: 1rem; color: #475569; font-style: italic; background: #f8fafc; }
    .img-toolbar {
        position: absolute;
        background: #1e293b;
        padding: 8px 12px;
        border-radius: 50px;
        display: flex;
        gap: 8px;
        z-index: 50;
        transform: translate(-50%, -130%);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .img-tool-btn { color: white; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; background: rgba(255,255,255,0.1); transition: all 0.2s; white-space: nowrap; cursor: pointer; border: none; }
    .img-tool-btn:hover { background: #0044CC; }
</style>
@endpush

@section('content')
<div id="content-app" class="space-y-8">
    <!-- Search Bar -->
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200">
        <div class="relative w-full md:w-64 group">
            <i class="ri-search-2-line absolute left-4 top-3 text-slate-400 group-focus-within:text-brand-600 transition-colors"></i>
            <input type="text" v-model="searchQuery" placeholder="Cari konten..."
                   class="w-full bg-slate-50 border border-transparent pl-11 pr-4 py-2.5 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition-all">
        </div>
    </div>

    <!-- Ulasan Section -->
    <section id="reviews">
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="header-section grad-orange flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4 z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/20">
                        <i class="ri-star-smile-fill text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg">Ulasan Pelanggan</h3>
                        <p class="text-xs text-orange-100 font-medium">Testimoni dari pelanggan</p>
                    </div>
                </div>
                <button @click="openModal('review', 'add')" class="h-10 px-6 rounded-xl bg-white text-orange-600 text-xs font-bold hover:bg-orange-50 transition shadow-lg flex items-center gap-2 z-10">
                    <i class="ri-add-line text-lg"></i> Tambah Ulasan
                </button>
            </div>

            <div class="table-container">
                <table class="w-full text-left">
                    <thead class="bg-orange-50 text-xs uppercase font-bold text-orange-800 sticky top-0">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">Inisial</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4 w-28">Rating</th>
                            <th class="px-6 py-4">Ulasan</th>
                            <th class="px-6 py-4 w-20 text-center">Status</th>
                            <th class="px-6 py-4 w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="review in filteredReviews" :key="review.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-100 to-orange-200 text-orange-600 font-black flex items-center justify-center mx-auto text-sm">
                                    @{{ getInitials(review.name) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">@{{ review.name }}</td>
                            <td class="px-6 py-4">
                                <div class="flex text-yellow-400">
                                    <i v-for="n in 5" :key="n" :class="n <= review.rating ? 'ri-star-fill' : 'ri-star-line'"></i>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-md">
                                <p class="whitespace-pre-wrap line-clamp-2">@{{ review.comment }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="review.is_active" @change="toggleStatus('review', review)" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openModal('review', 'edit', review)" class="w-8 h-8 rounded-lg bg-slate-50 text-amber-500 hover:bg-amber-50 transition flex items-center justify-center">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                    <button @click="confirmDelete('review', review)" class="w-8 h-8 rounded-lg bg-slate-50 text-red-500 hover:bg-red-50 transition flex items-center justify-center">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredReviews.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="ri-star-line text-4xl mb-2 block"></i>
                                Belum ada ulasan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq">
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="header-section grad-blue flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4 z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/20">
                        <i class="ri-question-answer-fill text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg">FAQ Manager</h3>
                        <p class="text-xs text-blue-100 font-medium">Pertanyaan yang sering diajukan</p>
                    </div>
                </div>
                <button @click="openModal('faq', 'add')" class="h-10 px-6 rounded-xl bg-white text-blue-600 text-xs font-bold hover:bg-blue-50 transition shadow-lg flex items-center gap-2 z-10">
                    <i class="ri-add-line text-lg"></i> Tambah FAQ
                </button>
            </div>

            <div class="table-container">
                <table class="w-full text-left">
                    <thead class="bg-blue-50 text-xs uppercase font-bold text-blue-800 sticky top-0">
                        <tr>
                            <th class="px-6 py-4 w-12">#</th>
                            <th class="px-6 py-4">Pertanyaan & Jawaban</th>
                            <th class="px-6 py-4 w-24 text-center">Status</th>
                            <th class="px-6 py-4 w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="(faq, index) in filteredFaqs" :key="faq.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-400 font-bold">@{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700 mb-2">@{{ faq.question }}</div>
                                <div class="text-xs text-slate-500 whitespace-pre-wrap line-clamp-2">@{{ faq.answer }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="faq.is_active" @change="toggleStatus('faq', faq)" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openModal('faq', 'edit', faq)" class="w-8 h-8 rounded-lg bg-slate-50 text-amber-500 hover:bg-amber-50 transition flex items-center justify-center">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                    <button @click="confirmDelete('faq', faq)" class="w-8 h-8 rounded-lg bg-slate-50 text-red-500 hover:bg-red-50 transition flex items-center justify-center">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredFaqs.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                <i class="ri-question-line text-4xl mb-2 block"></i>
                                Belum ada FAQ
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Two Column Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Bonus Files -->
        <section id="bonus-files">
            <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden h-full flex flex-col">
                <div class="header-section grad-purple flex justify-between items-center">
                    <div class="flex items-center gap-4 z-10">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/20">
                            <i class="ri-file-download-fill text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-lg">Bonus File</h3>
                            <p class="text-xs text-purple-100 font-medium">File download untuk member</p>
                        </div>
                    </div>
                    <button @click="openModal('file', 'add')" class="w-10 h-10 rounded-xl bg-white/20 text-white border border-white/20 hover:bg-white/30 transition flex items-center justify-center z-10">
                        <i class="ri-add-line text-lg"></i>
                    </button>
                </div>

                <div class="table-container flex-1">
                    <table class="w-full text-left">
                        <thead class="bg-purple-50 text-xs uppercase font-bold text-purple-800 sticky top-0">
                            <tr>
                                <th class="px-6 py-4">Nama File</th>
                                <th class="px-6 py-4 w-24">Link</th>
                                <th class="px-6 py-4 w-28 text-center">Level</th>
                                <th class="px-6 py-4 w-24 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="file in filteredFiles" :key="file.id" class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-slate-700 text-sm">@{{ file.title }}</td>
                                <td class="px-6 py-4">
                                    <a :href="file.file_path" target="_blank" class="text-xs font-bold text-blue-500 hover:underline flex items-center gap-1">
                                        <i class="ri-link"></i> Buka
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="getLevelBadge(file.required_level)" class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase">
                                        @{{ formatLevel(file.required_level) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openModal('file', 'edit', file)" class="w-7 h-7 rounded-lg bg-slate-50 text-amber-500 hover:bg-amber-50 transition flex items-center justify-center text-xs">
                                            <i class="ri-pencil-fill"></i>
                                        </button>
                                        <button @click="confirmDelete('file', file)" class="w-7 h-7 rounded-lg bg-slate-50 text-red-500 hover:bg-red-50 transition flex items-center justify-center text-xs">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredFiles.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="ri-file-line text-4xl mb-2 block"></i>
                                    Belum ada file
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Media Coverage -->
        <section id="media">
            <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden h-full flex flex-col">
                <div class="header-section grad-teal flex justify-between items-center">
                    <div class="flex items-center gap-4 z-10">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/20">
                            <i class="ri-newspaper-fill text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-lg">Media Liputan</h3>
                            <p class="text-xs text-teal-100 font-medium">Logo & link media partner</p>
                        </div>
                    </div>
                    <button @click="openModal('media', 'add')" class="w-10 h-10 rounded-xl bg-white/20 text-white border border-white/20 hover:bg-white/30 transition flex items-center justify-center z-10">
                        <i class="ri-add-line text-lg"></i>
                    </button>
                </div>

                <div class="table-container flex-1">
                    <table class="w-full text-left">
                        <thead class="bg-teal-50 text-xs uppercase font-bold text-teal-800 sticky top-0">
                            <tr>
                                <th class="px-6 py-4 w-16">Logo</th>
                                <th class="px-6 py-4">Nama Media</th>
                                <th class="px-6 py-4 w-28 text-center">URL</th>
                                <th class="px-6 py-4 w-24 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="media in filteredMedias" :key="media.id" class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <img :src="getMediaLogo(media)" class="h-8 w-auto object-contain bg-white rounded p-0.5 border border-slate-100">
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700">@{{ media.media_name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a :href="media.url" target="_blank" class="text-xs text-teal-600 hover:underline">
                                        <i class="ri-external-link-line"></i>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openModal('media', 'edit', media)" class="w-7 h-7 rounded-lg bg-slate-50 text-amber-500 hover:bg-amber-50 transition flex items-center justify-center text-xs">
                                            <i class="ri-pencil-fill"></i>
                                        </button>
                                        <button @click="confirmDelete('media', media)" class="w-7 h-7 rounded-lg bg-slate-50 text-red-500 hover:bg-red-50 transition flex items-center justify-center text-xs">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredMedias.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="ri-newspaper-line text-4xl mb-2 block"></i>
                                    Belum ada media
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Pages/Informasi Section -->
    <section id="pages">
        <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="header-section grad-indigo flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4 z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/20">
                        <i class="ri-information-fill text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg">Informasi Dashboard</h3>
                        <p class="text-xs text-indigo-100 font-medium">Halaman statis (Terms, About, dll)</p>
                    </div>
                </div>
                <button @click="openModal('page', 'add')" class="h-10 px-6 rounded-xl bg-white text-indigo-600 text-xs font-bold hover:bg-indigo-50 transition shadow-lg flex items-center gap-2 z-10">
                    <i class="ri-add-line text-lg"></i> Tambah Informasi
                </button>
            </div>

            <div class="table-container">
                <table class="w-full text-left">
                    <thead class="bg-indigo-50 text-xs uppercase font-bold text-indigo-800 sticky top-0">
                        <tr>
                            <th class="px-6 py-4 w-12">No</th>
                            <th class="px-6 py-4 w-1/4">Judul</th>
                            <th class="px-6 py-4">Konten (Preview)</th>
                            <th class="px-6 py-4 w-24 text-center">Status</th>
                            <th class="px-6 py-4 w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="(page, index) in filteredPages" :key="page.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-400">@{{ index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">@{{ page.title }}</td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-500 line-clamp-2" v-html="page.content"></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="page.is_active" @change="toggleStatus('page', page)" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-indigo-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openModal('page', 'edit', page)" class="w-8 h-8 rounded-lg bg-slate-50 text-amber-500 hover:bg-amber-50 transition flex items-center justify-center">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                    <button @click="confirmDelete('page', page)" class="w-8 h-8 rounded-lg bg-slate-50 text-red-500 hover:bg-red-50 transition flex items-center justify-center">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredPages.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="ri-file-text-line text-4xl mb-2 block"></i>
                                Belum ada halaman informasi
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Form Modal (Normal) -->
    <div v-if="modal.show && modal.type !== 'page'" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" @click.self="modal.show = false">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="font-black text-xl text-slate-800">@{{ modal.isEdit ? 'Edit Data' : 'Tambah Data Baru' }}</h3>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">@{{ modal.type }} Manager</p>
                </div>
                <button @click="modal.show = false" class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 space-y-5">
                <!-- Review Form -->
                <template v-if="modal.type === 'review'">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Pengguna</label>
                        <input v-model="formData.name" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500" placeholder="Contoh: Andi Saputra">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Rating</label>
                        <select v-model="formData.rating" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500">
                            <option value="5">5 - Sempurna</option>
                            <option value="4">4 - Bagus</option>
                            <option value="3">3 - Cukup</option>
                            <option value="2">2 - Kurang</option>
                            <option value="1">1 - Buruk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Komentar</label>
                        <textarea v-model="formData.comment" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500 resize-none" placeholder="Tulis ulasan..."></textarea>
                    </div>
                </template>

                <!-- FAQ Form -->
                <template v-if="modal.type === 'faq'">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pertanyaan</label>
                        <input v-model="formData.question" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jawaban</label>
                        <textarea v-model="formData.answer" rows="6" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500 resize-none" placeholder="Jawaban detail..."></textarea>
                    </div>
                </template>

                <!-- File Form -->
                <template v-if="modal.type === 'file'">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama File</label>
                        <input v-model="formData.title" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500" placeholder="Contoh: Script Auto Post V2">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Link Download</label>
                        <input v-model="formData.file_path" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500" placeholder="https://drive.google.com/...">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Deskripsi</label>
                        <textarea v-model="formData.description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500 resize-none" placeholder="Deskripsi file (opsional)"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Level Akses</label>
                        <select v-model="formData.required_level" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500">
                            <option value="visitor">Member</option>
                            <option value="reseller">Reseller</option>
                            <option value="reseller_vip">Reseller VIP</option>
                            <option value="reseller_vvip">Reseller VVIP</option>
                        </select>
                    </div>
                </template>

                <!-- Media Form -->
                <template v-if="modal.type === 'media'">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Media</label>
                        <input v-model="formData.media_name" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500" placeholder="Contoh: Detik.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">URL Logo</label>
                        <input v-model="formData.logo" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500" placeholder="https://...">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Link Liputan</label>
                        <input v-model="formData.url" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-500" placeholder="https://berita.com/...">
                    </div>
                </template>
            </div>

            <div class="p-6 border-t border-slate-100 bg-white">
                <button @click="submitForm" :disabled="loading" class="w-full h-12 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-lg shadow-brand-500/30 transition flex items-center justify-center gap-2 disabled:opacity-50">
                    <i v-if="loading" class="ri-loader-4-line animate-spin"></i>
                    <i v-else class="ri-save-3-fill"></i>
                    @{{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Page/Informasi Full-Screen Rich Editor Modal -->
    <div v-if="modal.show && modal.type === 'page'" class="fixed inset-0 z-[100] flex flex-col bg-white">
        <!-- Header Bar -->
        <div class="flex justify-between items-center px-4 md:px-8 py-4 bg-white border-b border-slate-200 flex-shrink-0">
            <div class="flex items-center gap-4 flex-1">
                <button @click="modal.show = false" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition">
                    <i class="ri-arrow-left-line text-xl"></i>
                </button>
                <input v-model="formData.title" type="text" placeholder="Masukkan Judul Artikel..."
                    class="text-lg md:text-2xl font-black text-slate-800 bg-transparent border-none outline-none flex-grow placeholder:text-slate-300 min-w-0">
            </div>
            <button @click="submitForm" :disabled="loading" class="bg-brand-600 hover:bg-brand-700 text-white px-4 md:px-6 py-2.5 rounded-lg font-bold shadow-lg shadow-brand-500/30 flex items-center gap-2 transition flex-shrink-0 disabled:opacity-50">
                <i v-if="loading" class="ri-loader-4-line animate-spin"></i>
                <i v-else class="ri-save-line"></i>
                <span class="hidden sm:inline">@{{ loading ? 'Menyimpan...' : 'Publikasi' }}</span>
            </button>
        </div>

        <!-- Rich Editor -->
        <div class="rich-editor-wrapper flex-1">
            <!-- Toolbar -->
            <div class="rich-toolbar">
                <button class="rich-btn" @click.prevent="execCmd('bold')" title="Bold"><i class="ri-bold"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('italic')" title="Italic"><i class="ri-italic"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('underline')" title="Underline"><i class="ri-underline"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('strikeThrough')" title="Strikethrough"><i class="ri-strikethrough"></i></button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button class="rich-btn" @click.prevent="execCmd('justifyLeft')" title="Rata Kiri"><i class="ri-align-left"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('justifyCenter')" title="Rata Tengah"><i class="ri-align-center"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('justifyRight')" title="Rata Kanan"><i class="ri-align-right"></i></button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button class="rich-btn" @click.prevent="execCmd('createLink')" title="Link URL"><i class="ri-link"></i></button>
                <button class="rich-btn" @click.prevent="triggerImageUpload" title="Upload Gambar"><i class="ri-image-add-line"></i></button>
                <input type="file" ref="imageUpload" @change="handleImageUpload" class="hidden" accept="image/*">
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button class="rich-btn" @click.prevent="execCmd('insertUnorderedList')" title="Bullet List"><i class="ri-list-unordered"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('insertOrderedList')" title="Numbered List"><i class="ri-list-ordered"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('formatBlock', 'H2')" title="Heading"><i class="ri-heading"></i></button>
                <button class="rich-btn" @click.prevent="execCmd('formatBlock', 'BLOCKQUOTE')" title="Quote"><i class="ri-double-quotes-l"></i></button>
            </div>

            <!-- Editor Area -->
            <div class="rich-content-container" @click="handleEditorClick">
                <div contenteditable="true" class="rich-content" @input="updateRichContent" ref="richEditor" data-placeholder="Mulai menulis artikel..."></div>

                <!-- Image Floating Toolbar -->
                <div v-if="selectedImg" class="img-toolbar" :style="{ top: toolbarPos.top + 'px', left: toolbarPos.left + 'px' }">
                    <button class="img-tool-btn" @click="resizeImg('25%')">Small</button>
                    <button class="img-tool-btn" @click="resizeImg('50%')">Medium</button>
                    <button class="img-tool-btn" @click="resizeImg('75%')">Large</button>
                    <button class="img-tool-btn" @click="resizeImg('100%')">Full</button>
                    <div class="w-px h-3 bg-white/20 mx-1"></div>
                    <button class="img-tool-btn" @click="alignImg('left')"><i class="ri-align-left"></i></button>
                    <button class="img-tool-btn" @click="alignImg('center')"><i class="ri-align-center"></i></button>
                    <button class="img-tool-btn" @click="alignImg('right')"><i class="ri-align-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="deleteModal.show" class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
        <div class="bg-white rounded-[2rem] p-8 w-full max-w-sm shadow-2xl text-center">
            <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center text-red-500 mx-auto mb-5 text-4xl">
                <i class="ri-delete-bin-2-line"></i>
            </div>
            <h3 class="font-black text-2xl text-slate-800 mb-2">Hapus Data?</h3>
            <p class="text-sm text-slate-500 mb-8">Data yang dihapus tidak dapat dikembalikan.</p>
            <div class="flex gap-3">
                <button @click="deleteModal.show = false" class="flex-1 py-3 px-6 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                <button @click="executeDelete" :disabled="loading" class="flex-1 py-3 px-6 rounded-xl bg-red-500 font-bold text-white hover:bg-red-600 shadow-lg shadow-red-500/30 transition disabled:opacity-50">
                    @{{ loading ? 'Menghapus...' : 'Ya, Hapus' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div v-if="toast.show" class="fixed top-6 left-1/2 -translate-x-1/2 z-[120]">
        <div class="bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-white/10 min-w-[300px]">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
                <i :class="toast.type === 'success' ? 'ri-checkbox-circle-fill' : 'ri-error-warning-fill'" class="text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm">@{{ toast.type === 'success' ? 'Berhasil!' : 'Error!' }}</h4>
                <p class="text-xs text-slate-300">@{{ toast.message }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            searchQuery: '',
            loading: false,
            reviews: @json($reviews),
            faqs: @json($faqs),
            files: @json($files),
            medias: @json($medias),
            pages: @json($pages),
            modal: { show: false, type: '', isEdit: false, id: null },
            formData: {},
            deleteModal: { show: false, type: '', item: null },
            toast: { show: false, message: '', type: 'success' },
            selectedImg: null,
            toolbarPos: { top: 0, left: 0 }
        }
    },
    computed: {
        filteredReviews() {
            return this.reviews.filter(r => r.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },
        filteredFaqs() {
            return this.faqs.filter(f => f.question.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },
        filteredFiles() {
            return this.files.filter(f => f.title.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },
        filteredMedias() {
            return this.medias.filter(m => m.media_name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },
        filteredPages() {
            return this.pages.filter(p => p.title.toLowerCase().includes(this.searchQuery.toLowerCase()));
        }
    },
    methods: {
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => this.toast.show = false, 3000);
        },
        getInitials(name) {
            return name.match(/(\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("").toUpperCase();
        },
        getLevelBadge(level) {
            const badges = {
                'visitor': 'badge-member',
                'reseller': 'badge-reseller',
                'reseller_vip': 'badge-vip',
                'reseller_vvip': 'badge-vvip'
            };
            return badges[level] || 'badge-member';
        },
        formatLevel(level) {
            const labels = {
                'visitor': 'Member',
                'reseller': 'Reseller',
                'reseller_vip': 'VIP',
                'reseller_vvip': 'VVIP'
            };
            return labels[level] || level;
        },
        getMediaLogo(media) {
            if (media.logo && media.logo.startsWith('http')) return media.logo;
            if (media.logo) return '/storage/' + media.logo;
            return 'https://via.placeholder.com/100x40?text=' + media.media_name;
        },
        openModal(type, mode, data = null) {
            this.modal = { show: true, type, isEdit: mode === 'edit', id: data?.id };
            this.selectedImg = null;
            if (mode === 'edit' && data) {
                this.formData = { ...data };
            } else {
                if (type === 'review') this.formData = { name: '', rating: 5, comment: '' };
                if (type === 'faq') this.formData = { question: '', answer: '' };
                if (type === 'file') this.formData = { title: '', file_path: '', description: '', required_level: 'visitor' };
                if (type === 'media') this.formData = { media_name: '', logo: '', url: '' };
                if (type === 'page') this.formData = { title: '', content: '' };
            }
            if (type === 'page') {
                this.$nextTick(() => {
                    if (this.$refs.richEditor) {
                        this.$refs.richEditor.innerHTML = this.formData.content || '';
                    }
                });
            }
        },
        // Rich Editor Methods
        execCmd(command, value = null) {
            if (command === 'createLink') {
                const url = prompt('Masukkan URL Link:', 'https://');
                if (url) document.execCommand(command, false, url);
            } else {
                document.execCommand(command, false, value);
            }
            if (this.$refs.richEditor) this.formData.content = this.$refs.richEditor.innerHTML;
        },
        triggerImageUpload() {
            this.$refs.imageUpload.click();
        },
        handleImageUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    if (this.$refs.richEditor) this.$refs.richEditor.focus();
                    document.execCommand('insertImage', false, ev.target.result);
                    this.formData.content = this.$refs.richEditor.innerHTML;
                };
                reader.readAsDataURL(file);
            }
            e.target.value = '';
        },
        updateRichContent(event) {
            this.formData.content = event.target.innerHTML;
        },
        handleEditorClick(e) {
            if (e.target.tagName === 'IMG') {
                this.selectedImg = e.target;
                const imgs = this.$refs.richEditor.querySelectorAll('img');
                imgs.forEach(img => img.classList.remove('selected'));
                e.target.classList.add('selected');
                this.toolbarPos = {
                    top: e.target.offsetTop,
                    left: e.target.offsetLeft + (e.target.offsetWidth / 2)
                };
            } else {
                if (this.selectedImg) {
                    this.selectedImg.classList.remove('selected');
                    this.selectedImg = null;
                }
            }
        },
        resizeImg(size) {
            if (this.selectedImg) {
                this.selectedImg.style.width = size;
                this.formData.content = this.$refs.richEditor.innerHTML;
            }
        },
        alignImg(align) {
            if (this.selectedImg) {
                this.selectedImg.style.display = 'block';
                if (align === 'center') this.selectedImg.style.margin = '1.5rem auto';
                if (align === 'left') this.selectedImg.style.margin = '1.5rem auto 1.5rem 0';
                if (align === 'right') this.selectedImg.style.margin = '1.5rem 0 1.5rem auto';
                this.formData.content = this.$refs.richEditor.innerHTML;
            }
        },
        async submitForm() {
            this.loading = true;
            const type = this.modal.type;
            const isEdit = this.modal.isEdit;
            const id = this.modal.id;

            const endpoints = {
                review: isEdit ? `/admin/content/reviews/${id}` : '/admin/content/reviews',
                faq: isEdit ? `/admin/content/faqs/${id}` : '/admin/content/faqs',
                file: isEdit ? `/admin/content/files/${id}` : '/admin/content/files',
                media: isEdit ? `/admin/content/medias/${id}` : '/admin/content/medias',
                page: isEdit ? `/admin/content/pages/${id}` : '/admin/content/pages'
            };

            try {
                const res = await fetch(endpoints[type], {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });
                const data = await res.json();

                if (data.success) {
                    if (isEdit) {
                        const arr = this[type + 's'] || this[type === 'faq' ? 'faqs' : type + 's'];
                        const idx = arr.findIndex(x => x.id === id);
                        if (idx !== -1) arr[idx] = data.data;
                    } else {
                        const arrName = type === 'review' ? 'reviews' : type === 'faq' ? 'faqs' : type === 'file' ? 'files' : type === 'media' ? 'medias' : 'pages';
                        this[arrName].unshift(data.data);
                    }
                    this.modal.show = false;
                    this.showToast(data.message);
                } else {
                    this.showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            } catch (e) {
                this.showToast('Terjadi kesalahan', 'error');
            }
            this.loading = false;
        },
        confirmDelete(type, item) {
            this.deleteModal = { show: true, type, item };
        },
        async executeDelete() {
            this.loading = true;
            const { type, item } = this.deleteModal;

            const endpoints = {
                review: `/admin/content/reviews/${item.id}`,
                faq: `/admin/content/faqs/${item.id}`,
                file: `/admin/content/files/${item.id}`,
                media: `/admin/content/medias/${item.id}`,
                page: `/admin/content/pages/${item.id}`
            };

            try {
                const res = await fetch(endpoints[type], {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (data.success) {
                    const arrName = type === 'review' ? 'reviews' : type === 'faq' ? 'faqs' : type === 'file' ? 'files' : type === 'media' ? 'medias' : 'pages';
                    this[arrName] = this[arrName].filter(x => x.id !== item.id);
                    this.deleteModal.show = false;
                    this.showToast(data.message);
                } else {
                    this.showToast(data.message || 'Gagal menghapus', 'error');
                }
            } catch (e) {
                this.showToast('Terjadi kesalahan', 'error');
            }
            this.loading = false;
        },
        async toggleStatus(type, item) {
            const endpoints = {
                review: `/admin/content/reviews/${item.id}/toggle`,
                faq: `/admin/content/faqs/${item.id}/toggle`,
                file: `/admin/content/files/${item.id}/toggle`,
                page: `/admin/content/pages/${item.id}/toggle`
            };

            try {
                const res = await fetch(endpoints[type], {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (data.success) {
                    item.is_active = data.is_active;
                    this.showToast(data.message);
                }
            } catch (e) {
                this.showToast('Terjadi kesalahan', 'error');
            }
        }
    }
}).mount('#content-app');
</script>
@endpush
@endsection
