@extends('layouts.admin')

@section('title', 'Notifikasi')
@section('page-title', 'Pengaturan Notifikasi')
@section('page-description', 'Atur notifikasi WhatsApp & Email ke pelanggan')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.notifications.update') }}" method="POST">
        @csrf
        @method('PUT')

        @php $globalIndex = 0; @endphp

        <!-- WhatsApp Notifications -->
        <div class="glass-panel rounded-[2rem] overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-5 flex items-center gap-3 text-white">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/20">
                    <i class="ri-whatsapp-fill text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg">WhatsApp Notifications</h3>
                    <p class="text-xs text-green-100 font-medium">Notifikasi otomatis via WhatsApp ke pelanggan</p>
                </div>
            </div>

            <div class="p-6 space-y-4">
                @foreach($grouped['whatsapp'] ?? [] as $notification)
                @php
                    $template = $notification->config['message_template'] ?? '';
                    $defaultTemplates = [
                        'order_created' => "Halo {nama}! 👋\n\nPesanan Anda telah dibuat:\n📦 Produk: {produk}\n🆔 Order ID: {order_id}\n💰 Total: Rp {total}\n\nSilakan selesaikan pembayaran sebelum expired.\n\nTerima kasih! 🙏",
                        'order_paid' => "Halo {nama}! ✅\n\nPembayaran Anda telah dikonfirmasi:\n📦 Produk: {produk}\n🆔 Order ID: {order_id}\n💰 Total: Rp {total}\n\nPesanan sedang diproses, mohon tunggu ya! ⏳",
                        'order_completed' => "Halo {nama}! 🎉\n\nPesanan Anda telah selesai:\n📦 Produk: {produk}\n🆔 Order ID: {order_id}\n\nTerima kasih telah berbelanja! ⭐\nJangan lupa beri rating ya!",
                        'order_failed' => "Halo {nama}! ❌\n\nMohon maaf, pesanan Anda gagal diproses:\n📦 Produk: {produk}\n🆔 Order ID: {order_id}\n\nDana akan dikembalikan dalam 1x24 jam.\nHubungi admin jika butuh bantuan.",
                    ];
                    $currentTemplate = !empty($template) ? $template : ($defaultTemplates[$notification->key] ?? '');
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="flex items-center justify-between p-4 bg-slate-50 border-b border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="ri-message-2-fill text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $notification->label }}</h4>
                                <p class="text-xs text-slate-500">{{ $notification->description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="notifications[{{ $globalIndex }}][id]" value="{{ $notification->id }}">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="notifications[{{ $globalIndex }}][is_enabled]" value="1"
                                       class="sr-only peer" {{ $notification->is_enabled ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Editable Message Template -->
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Template Pesan</label>
                            <button type="button" onclick="togglePreview({{ $globalIndex }})" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition">
                                <i class="ri-eye-line"></i> Preview
                            </button>
                        </div>
                        <textarea name="notifications[{{ $globalIndex }}][message_template]"
                                  id="template-{{ $globalIndex }}"
                                  rows="6"
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-400/20 transition resize-none font-mono leading-relaxed"
                                  placeholder="Tulis template pesan WhatsApp...">{{ $currentTemplate }}</textarea>

                        <!-- Preview Box -->
                        <div id="preview-{{ $globalIndex }}" class="hidden mt-3">
                            <div class="bg-[#E5DDD5] rounded-xl p-4 relative">
                                <div class="bg-[#DCF8C6] rounded-xl rounded-tr-none p-3 max-w-[90%] ml-auto shadow-sm">
                                    <p id="preview-text-{{ $globalIndex }}" class="text-sm text-slate-800 whitespace-pre-wrap leading-relaxed"></p>
                                    <div class="text-right mt-1">
                                        <span class="text-[10px] text-slate-500">12:00 <i class="ri-check-double-fill text-blue-500"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Variable Tags -->
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase mr-1 self-center">Variabel:</span>
                            @foreach(['{nama}' => 'Nama', '{order_id}' => 'Order ID', '{produk}' => 'Produk', '{total}' => 'Total', '{status}' => 'Status', '{link}' => 'Link'] as $var => $label)
                            <button type="button" onclick="insertVar({{ $globalIndex }}, '{{ $var }}')"
                                    class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-[11px] font-bold hover:bg-green-100 transition border border-green-200">
                                {{ $var }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @php $globalIndex++; @endphp
                @endforeach

                @if(empty($grouped['whatsapp'] ?? []))
                <div class="text-center py-8 text-slate-400">
                    <i class="ri-whatsapp-line text-4xl mb-2 block"></i>
                    <p class="text-sm font-medium">Belum ada pengaturan WhatsApp</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Email Notifications -->
        <div class="glass-panel rounded-[2rem] overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-red-500 to-pink-500 p-5 flex items-center gap-3 text-white">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/20">
                    <i class="ri-mail-fill text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg">Email Notifications</h3>
                    <p class="text-xs text-red-100 font-medium">Notifikasi via Email ke admin</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                @foreach($grouped['email'] ?? [] as $notification)
                <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-slate-300 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                            <i class="ri-mail-line text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800">{{ $notification->label }}</h4>
                            <p class="text-xs text-slate-500">{{ $notification->description }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="notifications[{{ $globalIndex }}][id]" value="{{ $notification->id }}">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notifications[{{ $globalIndex }}][is_enabled]" value="1"
                                   class="sr-only peer" {{ $notification->is_enabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                        </label>
                    </div>
                </div>
                @php $globalIndex++; @endphp
                @endforeach

                @if(empty($grouped['email'] ?? []))
                <div class="text-center py-8 text-slate-400">
                    <i class="ri-mail-line text-4xl mb-2 block"></i>
                    <p class="text-sm font-medium">Belum ada pengaturan Email</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Info Card -->
        <div class="glass-panel rounded-[2rem] p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="ri-lightbulb-fill text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 mb-1">Variabel yang Tersedia</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Gunakan variabel berikut dalam template pesan:
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700">{nama}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700">{order_id}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700">{produk}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700">{total}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700">{status}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700">{link}</code>
                        — variabel akan diganti dengan data asli saat mengirim notifikasi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-1 transition-all flex items-center gap-2">
                <i class="ri-save-line text-lg"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePreview(index) {
    const preview = document.getElementById('preview-' + index);
    const textarea = document.getElementById('template-' + index);
    const previewText = document.getElementById('preview-text-' + index);

    if (preview.classList.contains('hidden')) {
        // Show preview with sample data
        let text = textarea.value;
        text = text.replace(/\{nama\}/g, 'Ahmad Rizky');
        text = text.replace(/\{order_id\}/g, 'ORD-20260207-ABC');
        text = text.replace(/\{produk\}/g, 'Mobile Legends 86 Diamonds');
        text = text.replace(/\{total\}/g, '25.000');
        text = text.replace(/\{status\}/g, 'Completed');
        text = text.replace(/\{link\}/g, 'https://marspedia.id/order/123');
        previewText.textContent = text;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}

function insertVar(index, variable) {
    const textarea = document.getElementById('template-' + index);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + variable.length, start + variable.length);
}
</script>
@endpush
@endsection
