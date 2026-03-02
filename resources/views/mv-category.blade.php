<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $category['name'] }} - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --brand-primary: #0033AA;
            --brand-secondary: #002288;
            --brand-accent: #3366FF;
            --bg-page: #F2F2F7;
            --surface: #FFFFFF;
            --ios-input: #F3F4F6;
        }
        /* Modern Popup */
        .popup-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px; animation: popupFadeIn 0.2s ease; }
        .popup-overlay.active { display: flex; }
        @keyframes popupFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes popupSlideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .popup-box { background: white; border-radius: 20px; padding: 30px 24px 24px; max-width: 360px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.15); animation: popupSlideUp 0.3s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .popup-icon { font-size: 48px; margin-bottom: 16px; }
        .popup-icon.warning { color: #F59E0B; }
        .popup-icon.error { color: #EF4444; }
        .popup-title { font-size: 16px; font-weight: 700; color: #1E293B; margin-bottom: 8px; }
        .popup-message { font-size: 13px; color: #64748B; line-height: 1.5; margin-bottom: 20px; }
        .popup-btn { width: 100%; padding: 14px; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; background: var(--brand-primary); color: white; }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-page);
            color: #1C1C1E;
            -webkit-tap-highlight-color: transparent;
            scroll-behavior: smooth;
        }

        h1, h2, h3, h4, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }

        .touch-effect { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .touch-effect:active { transform: scale(0.96); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .item-animate { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }

        @keyframes bounce-short { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .animate-bounce-short { animation: bounce-short 0.4s ease-in-out; }

        @keyframes bounce-slow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .animate-bounce-slow { animation: bounce-slow 2s infinite ease-in-out; }

        @keyframes pulse-strong {
            0% { box-shadow: 0 0 0 0 rgba(0, 51, 170, 0.5); }
            70% { box-shadow: 0 0 0 14px rgba(0, 51, 170, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 51, 170, 0); }
        }
        .btn-pulse { animation: pulse-strong 2s infinite; }

        .card-ios {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
        }

        .input-ios {
            background-color: var(--ios-input);
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        .input-ios:focus {
            background-color: #FFFFFF;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(0, 51, 170, 0.1);
        }

        .card-selected {
            border: 2px solid var(--brand-primary) !important;
            background-color: #F0F9FF !important;
            position: relative;
            transform: scale(1.02);
            z-index: 10;
            box-shadow: 0 8px 20px rgba(0, 51, 170, 0.15);
        }

        .badge-check {
            position: absolute; top: -1px; right: -1px;
            background: var(--brand-primary); color: white;
            border-bottom-left-radius: 12px; border-top-right-radius: 20px;
            width: 26px; height: 26px; display: flex; align-items: center; justify-content: center;
            font-size: 10px; z-index: 10;
        }

        .ribbon-promo {
            position: absolute; top: 0; left: 0;
            background: linear-gradient(135deg, #F43F5E, #E11D48);
            color: white; font-size: 9px; font-weight: 800;
            padding: 3px 10px; border-bottom-right-radius: 12px; border-top-left-radius: 20px;
            z-index: 10;
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { primary: '#0033AA', secondary: '#002288', accent: '#3366FF' } }
                }
            }
        }
    </script>
</head>
<body class="antialiased pb-36 lg:pb-28">
<!-- Modern Popup -->
<div class="popup-overlay" id="modernPopup">
    <div class="popup-box">
        <div class="popup-icon warning" id="popupIcon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="popup-title" id="popupTitle">PERHATIAN !</div>
        <div class="popup-message" id="popupMessage"></div>
        <button class="popup-btn" onclick="closePopup()">BAIK, MENGERTI !</button>
    </div>
</div>

<div class="min-h-screen flex flex-col items-center">
    <!-- Header -->
    <header class="w-full max-w-[680px] lg:max-w-[1080px] bg-brand-primary/90 backdrop-blur-xl lg:rounded-b-[32px] shadow-lg sticky top-0 z-50 transition-all duration-300 border-b border-white/10">
        <div class="px-4 py-2.5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white touch-effect group">
                <div class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center group-active:bg-white/25 transition border border-white/10 backdrop-blur-md">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </div>
                <div>
                    <h1 class="text-sm font-heading font-bold leading-tight uppercase tracking-wider">{{ strtoupper(config('app.name')) }}</h1>
                    <p class="text-[10px] text-white/80 font-medium tracking-wide">Official Store</p>
                </div>
            </a>

            <div class="flex items-center gap-1.5">
                <button onclick="toggleSearch()" class="w-9 h-9 rounded-full flex items-center justify-center text-white hover:bg-white/10 touch-effect transition">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                <a href="https://wa.me/{{ config('whatsapp.fonnte.sender', '6281234567890') }}" target="_blank" class="w-9 h-9 rounded-full flex items-center justify-center text-white hover:bg-white/10 touch-effect transition relative">
                    <i class="fa-solid fa-headset text-sm"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-green-400 rounded-full border-2 border-brand-primary"></span>
                </a>

                <a href="{{ route('track.order') }}" class="flex items-center gap-1.5 bg-white text-brand-primary px-3.5 py-2 rounded-full text-[11px] font-bold shadow-lg hover:bg-gray-50 touch-effect transition-transform ml-0.5">
                    <i class="fa-solid fa-receipt text-[10px]"></i>
                    <span>Cek Pesanan</span>
                </a>
            </div>
        </div>

        <!-- Search Bar (hidden by default) -->
        <div id="searchBar" class="hidden px-4 pb-4">
            <div class="relative item-animate">
                <input type="text" id="headerSearchInput" placeholder="Cari produk lain..." class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/15 border border-white/20 text-white placeholder-white/60 text-sm focus:outline-none focus:bg-white/25 transition-all backdrop-blur-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-white/60 text-sm"></i>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-[680px] lg:max-w-[1080px] px-3 lg:px-0 py-5 grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- Left Column -->
        <div class="lg:col-span-7 space-y-5">

            <!-- Game Info Card -->
            <div class="card-ios p-4 relative overflow-hidden item-animate">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="relative shrink-0 animate-float">
                        <div class="p-1 bg-white rounded-[20px] shadow-lg">
                            <img src="{{ $category['image'] }}" class="w-16 h-16 rounded-[14px] object-cover" alt="{{ $category['name'] }}" onerror="this.src='/images/default-game.png'">
                        </div>
                    </div>

                    <div class="flex-1 w-full">
                        <div class="flex justify-between items-start mb-1 relative">
                            <h2 class="font-heading font-extrabold text-base text-slate-900 leading-tight flex items-center gap-1.5">
                                {{ $category['name'] }}
                                <i class="fa-solid fa-circle-check text-blue-500 text-sm" title="Verified"></i>
                            </h2>
                        </div>

                        <div class="flex items-center gap-1.5 mb-3">
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-700">5.0</span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div class="bg-gradient-to-br from-emerald-100/90 via-teal-50 to-white border border-emerald-200/60 rounded-xl p-2 flex flex-col items-center justify-center text-center gap-0.5 animate-bounce-slow shadow-sm" style="animation-delay: 0s;">
                                <div class="w-6 h-6 rounded-full bg-white/70 flex items-center justify-center shadow-sm mb-0.5">
                                    <i class="fa-solid fa-bolt text-emerald-600 text-sm"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-emerald-900 leading-none">Proses</span>
                                <span class="text-[9px] font-semibold text-emerald-700/80 leading-none">Cepat</span>
                            </div>

                            <div class="bg-gradient-to-br from-blue-100/90 via-indigo-50 to-white border border-blue-200/60 rounded-xl p-2 flex flex-col items-center justify-center text-center gap-0.5 animate-bounce-slow shadow-sm" style="animation-delay: 0.2s;">
                                <div class="w-6 h-6 rounded-full bg-white/70 flex items-center justify-center shadow-sm mb-0.5">
                                    <i class="fa-solid fa-clock text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-blue-900 leading-none">Layanan</span>
                                <span class="text-[9px] font-semibold text-blue-700/80 leading-none">24 Jam</span>
                            </div>

                            <div class="bg-gradient-to-br from-orange-100/90 via-amber-50 to-white border border-orange-200/60 rounded-xl p-2 flex flex-col items-center justify-center text-center gap-0.5 animate-bounce-slow shadow-sm" style="animation-delay: 0.4s;">
                                <div class="w-6 h-6 rounded-full bg-white/70 flex items-center justify-center shadow-sm mb-0.5">
                                    <i class="fa-solid fa-shield-halved text-orange-600 text-sm"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-orange-900 leading-none">Garansi</span>
                                <span class="text-[9px] font-semibold text-orange-700/80 leading-none">100%</span>
                            </div>
                        </div>

                        @if(!empty($category['description']))
                        <div class="mt-3">
                            <button onclick="toggleDescription()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg bg-brand-primary/10 text-brand-primary font-bold text-xs transition-all duration-300 hover:bg-brand-primary/20 active:scale-95 group">
                                <span id="descBtnText">Lihat Deskripsi</span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300 text-xs" id="descIcon"></i>
                            </button>
                            <div id="descContent" class="hidden mt-2 p-3 bg-gray-50 rounded-xl text-[11px] text-slate-600 leading-relaxed border border-gray-100 shadow-sm">
                                {!! $category['description'] !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div id="section-nominal" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 100ms;">
                <div class="bg-gray-50/80 backdrop-blur-sm px-4 py-3 border-b border-gray-100 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-brand-primary text-white flex items-center justify-center font-bold text-[11px] shadow-lg shadow-brand-primary/20">1</div>
                    <h3 class="font-bold text-slate-800 text-sm">Pilih Produk</h3>
                </div>

                <div class="p-4">
                    @php $variantNames = collect($variants)->pluck('name')->toArray(); @endphp

                    @if(count($variantNames) > 1)
                    <div class="flex gap-1.5 overflow-x-auto pb-3 mb-2 no-scrollbar">
                        @foreach($variantNames as $index => $name)
                        <button onclick="setActiveCategory('{{ $name }}')"
                                class="category-pill px-3.5 py-2 rounded-full text-[11px] font-bold whitespace-nowrap transition-all duration-300 touch-effect border {{ $index === 0 ? 'bg-slate-900 text-white border-slate-900 shadow-lg shadow-slate-200' : 'bg-white text-slate-500 border-gray-200 hover:bg-gray-50' }}"
                                data-category="{{ $name }}">
                            {{ $name }}
                        </button>
                        @endforeach
                    </div>
                    @endif

                    @foreach($variants as $index => $group)
                    <div class="variant-group {{ $index > 0 ? 'hidden' : '' }}" data-variant="{{ $group['name'] }}">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            @foreach($group['items'] as $item)
                            <div onclick="selectProduct(this, '{{ $item['sku'] }}', {{ $item['price'] }}, '{{ addslashes($item['name']) }}')"
                                 class="product-card border rounded-[18px] p-3.5 cursor-pointer transition-all duration-300 touch-effect relative group bg-white h-full flex flex-col justify-between hover:shadow-lg hover:-translate-y-1 border-gray-100"
                                 data-sku="{{ $item['sku'] }}">

                                @if($item['original_price'] > $item['price'])
                                <div class="ribbon-promo">PROMO</div>
                                @endif

                                <div class="badge-check hidden"><i class="fa-solid fa-check"></i></div>

                                <div class="flex items-center gap-2 mb-2.5 mt-0.5">
                                    @if(!empty($item['image']))
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                        <img src="{{ $item['image'] }}" class="w-4 h-4 object-contain drop-shadow-sm transition duration-300" alt="{{ $item['name'] }}">
                                    </div>
                                    @else
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-gem text-blue-500 text-xs"></i>
                                    </div>
                                    @endif
                                    <span class="text-[11px] font-bold text-slate-700 leading-tight group-hover:text-brand-primary transition-colors">{{ $item['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-extrabold text-brand-primary tracking-tight">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    @if($item['original_price'] > $item['price'])
                                    <p class="text-[10px] text-slate-400 line-through">Rp {{ number_format($item['original_price'], 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="lg:col-span-5 space-y-5">

            <!-- Payment Methods -->
            <div id="section-payment" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 200ms;">
                <div class="bg-gray-50/80 backdrop-blur-sm px-4 py-3 border-b border-gray-100 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-brand-primary text-white flex items-center justify-center font-bold text-[11px] shadow-lg shadow-brand-primary/20">2</div>
                    <h3 class="font-bold text-slate-800 text-sm">Metode Pembayaran</h3>
                </div>

                <div class="p-4 space-y-3">
                    <div id="paymentPlaceholder" class="text-center py-8 bg-gray-50 rounded-[18px] border border-dashed border-gray-200">
                        <i class="fa-solid fa-basket-shopping text-gray-300 text-2xl mb-2 animate-bounce"></i>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mt-2">Pilih Produk Terlebih Dahulu</p>
                    </div>

                    <div id="paymentContainer" class="hidden space-y-3">
                        @foreach($paymentMethods as $group)
                        <div class="payment-group border border-gray-100 rounded-[18px] overflow-hidden bg-white shadow-sm hover:shadow-md transition-all">
                            <button onclick="togglePaymentGroup(this)" class="w-full px-4 py-3 bg-gray-50/50 flex items-center justify-between transition-colors hover:bg-gray-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-slate-500 shadow-sm">
                                        <i class="fa-solid fa-wallet text-[10px]"></i>
                                    </div>
                                    <h4 class="text-[11px] font-bold text-slate-700 uppercase tracking-wide">{{ $group['name'] }}</h4>
                                </div>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-300 group-icon"></i>
                            </button>

                            <div class="payment-items hidden p-3 space-y-2 bg-white border-t border-gray-100">
                                @foreach($group['channels'] as $channel)
                                <div onclick="selectPayment(this, '{{ $channel['code'] }}', {{ $channel['fee'] }}, '{{ $channel['fee_type'] }}')"
                                     class="payment-card border rounded-[14px] p-3 cursor-pointer relative overflow-hidden touch-effect transition-all duration-300 group flex items-center justify-between hover:border-brand-primary/30 hover:shadow-lg hover:-translate-y-1 border-gray-100 bg-white"
                                     data-code="{{ $channel['code'] }}">

                                    <div class="badge-check hidden"><i class="fa-solid fa-check"></i></div>

                                    <div class="flex items-center gap-3 flex-1 overflow-hidden">
                                        <div class="w-10 h-6 bg-white border border-gray-100 rounded flex items-center justify-center p-0.5 shadow-sm shrink-0">
                                            <img src="{{ $channel['image'] }}" class="h-full object-contain" onerror="this.src='/images/default-payment.png'">
                                        </div>
                                        <div class="flex flex-col truncate">
                                            <span class="text-[11px] font-bold text-slate-800 truncate">{{ $channel['name'] }}</span>
                                            <span class="text-[9px] text-slate-400 truncate mt-0.5">
                                                Biaya:
                                                @if($channel['fee'] == 0)
                                                    Gratis
                                                @elseif($channel['fee_type'] === 'percentage')
                                                    {{ $channel['fee'] }}%
                                                @else
                                                    Rp {{ number_format($channel['fee'], 0, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 pl-2">
                                        <p class="payment-price text-xs font-extrabold text-brand-primary">-</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- User Data -->
            @php
                $categoryType = strtolower($category['category_type'] ?? '');
                $productName = strtolower($category['name'] ?? '');

                // Determine section title based on category and product
                if ($categoryType === 'games') {
                    $sectionTitle = 'Data Akun Game';
                } elseif ($categoryType === 'pulsa & data') {
                    $sectionTitle = 'Data Nomor HP';
                } elseif ($categoryType === 'e-money') {
                    $sectionTitle = 'Data E-Wallet';
                } elseif (str_contains($productName, 'pln') || str_contains($productName, 'listrik')) {
                    $sectionTitle = 'Data Pelanggan PLN';
                } elseif (str_contains($productName, 'pdam') || str_contains($productName, 'air')) {
                    $sectionTitle = 'Data Pelanggan PDAM';
                } else {
                    $sectionTitle = 'Data Pembelian';
                }
            @endphp
            <div id="section-userdata" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 300ms;">
                <div class="bg-gray-50/80 backdrop-blur-sm px-4 py-3 border-b border-gray-100 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-brand-primary text-white flex items-center justify-center font-bold text-[11px] shadow-lg shadow-brand-primary/20">3</div>
                    <h3 class="font-bold text-slate-800 text-sm">{{ $sectionTitle }}</h3>
                </div>

                <div class="p-4">
                    @if(!empty($category['info_form']))
                    <div class="mb-3 bg-yellow-50/70 border border-yellow-100 p-3 rounded-xl flex gap-2.5 items-start">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-sm mt-0.5"></i>
                        <p class="text-[11px] text-slate-700 leading-relaxed font-medium">{!! $category['info_form'] !!}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        @foreach($category['form_fields'] as $field)
                        <div class="group {{ count($category['form_fields']) == 1 ? 'col-span-2' : '' }}">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 block ml-0.5">{{ $field['label'] }}</label>
                            <input type="{{ $field['type'] }}" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                   placeholder="{{ $field['placeholder'] }}" {{ $field['required'] ? 'required' : '' }}
                                   class="w-full px-3.5 py-3 rounded-xl input-ios text-sm font-bold text-slate-800 outline-none placeholder-slate-400">
                        </div>
                        @endforeach
                    </div>

                    @if($category['check_id'])
                    <button type="button" onclick="validateAccount()" id="validateBtn" class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs transition-all duration-300 hover:bg-slate-200 active:scale-95">
                        <i class="fa-solid fa-search text-[10px]"></i>
                        <span>Cek Akun</span>
                    </button>

                    <div id="accountResult" class="hidden mt-3 bg-emerald-50 border border-emerald-200 rounded-xl p-3 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500">Nickname:</span>
                            <span id="resultNickname" class="block text-sm font-bold text-slate-800">-</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contact Info -->
            <div id="section-contact" class="card-ios overflow-hidden transition-all duration-300 item-animate" style="animation-delay: 400ms;">
                <div class="bg-gray-50/80 backdrop-blur-sm px-4 py-3 border-b border-gray-100 flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-brand-primary text-white flex items-center justify-center font-bold text-[11px] shadow-lg shadow-brand-primary/20">4</div>
                    <h3 class="font-bold text-slate-800 text-sm">Data Pembeli</h3>
                </div>

                <div class="p-4 space-y-4">
                    <div class="flex items-center justify-between mb-1">
                        <span></span>
                        <button type="button" onclick="clearBuyerData()" class="text-[10px] font-bold text-slate-400 border border-slate-200 rounded-lg px-2.5 py-1 hover:bg-slate-50">
                            <i class="fa-solid fa-eraser"></i> Clear
                        </button>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 block ml-0.5">WhatsApp <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <i class="fa-brands fa-whatsapp text-sm text-green-500"></i>
                            </div>
                            <input type="tel" id="whatsapp" placeholder="08xxxxxxxxxx"
                                   class="w-full pl-10 pr-3.5 py-3 rounded-xl input-ios text-sm font-bold text-slate-800 outline-none placeholder-slate-400">
                        </div>
                    </div>

                    <div class="group">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 block ml-0.5">Email <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                <i class="fa-solid fa-envelope text-sm text-blue-500"></i>
                            </div>
                            <input type="email" id="buyer_email" placeholder="nama@email.com"
                                   class="w-full pl-10 pr-3.5 py-3 rounded-xl input-ios text-sm font-bold text-slate-800 outline-none placeholder-slate-400">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-emerald-50/70 p-3 rounded-xl border border-emerald-100/70">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="fa-solid fa-receipt text-sm"></i>
                        </div>
                        <p class="text-[11px] text-emerald-900 font-medium leading-relaxed">
                            Invoice bukti transaksi akan dikirimkan otomatis ke WhatsApp & Email Anda.
                        </p>
                    </div>

                    <button onclick="processOrder()" id="submitBtn"
                            class="w-full bg-gradient-to-r from-brand-primary to-brand-secondary hover:to-brand-primary text-white btn-pulse shadow-xl shadow-brand-primary/30 font-bold py-3 rounded-xl text-sm transition-all touch-effect flex items-center justify-center gap-2">
                        <span id="btnText">BELI SEKARANG</span>
                        <span id="btnLoading" class="hidden"><i class="fa-solid fa-spinner fa-spin"></i> Memproses...</span>
                        <i class="fa-solid fa-arrow-right animate-bounce text-sm" id="btnArrow"></i>
                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Bar -->
    <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-[680px] lg:max-w-[1080px] z-50 pointer-events-none">
        <div class="bg-white/90 backdrop-blur-xl border-t border-white/20 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] rounded-t-[28px] p-4 lg:px-6 pointer-events-auto transition-transform duration-300 min-h-[76px] flex items-center justify-between gap-4 border-x border-white/20">

            <div class="flex flex-col flex-1 min-w-0">
                <div id="bottomItemName" class="text-[11px] font-medium text-slate-400 italic mb-0.5">
                    Pilih produk dulu...
                </div>
                <div class="flex items-center gap-1.5">
                    <span id="bottomTotalPrice" class="text-xl font-extrabold text-brand-primary leading-none tracking-tight">Rp 0</span>
                </div>
            </div>

            <button onclick="processOrder()"
                    class="flex-[1.3] max-w-[220px] bg-gradient-to-r from-brand-primary to-brand-secondary hover:to-brand-primary text-white btn-pulse shadow-xl shadow-brand-primary/30 font-bold py-3 px-5 rounded-lg text-xs lg:text-sm transition-all touch-effect flex items-center justify-center gap-1.5 hover:-translate-y-1">
                <span>BELI SEKARANG</span>
                <i class="fa-solid fa-arrow-right animate-bounce text-xs"></i>
            </button>
        </div>
    </div>
</div>

<script>
let selectedProduct = null;
let selectedPayment = null;
let validatedNickname = null;

function showPopup(message, type = 'warning') {
    const popup = document.getElementById('modernPopup');
    const icon = document.getElementById('popupIcon');
    const title = document.getElementById('popupTitle');
    const msg = document.getElementById('popupMessage');
    const icons = { warning: 'fa-exclamation-triangle', error: 'fa-times-circle' };
    const titles = { warning: 'PERHATIAN !', error: 'GAGAL !' };
    icon.className = 'popup-icon ' + type;
    icon.innerHTML = '<i class="fas ' + (icons[type] || icons.warning) + '"></i>';
    title.textContent = titles[type] || titles.warning;
    msg.textContent = message;
    popup.classList.add('active');
}
function closePopup() { document.getElementById('modernPopup').classList.remove('active'); }

// Auto-save buyer data
function saveBuyerData() {
    localStorage.setItem('buyer_phone', document.getElementById('whatsapp').value);
    if (document.getElementById('buyer_email')) localStorage.setItem('buyer_email', document.getElementById('buyer_email').value);
}
function loadBuyerData() {
    const phone = localStorage.getItem('buyer_phone');
    const email = localStorage.getItem('buyer_email');
    if (phone) document.getElementById('whatsapp').value = phone;
    if (email && document.getElementById('buyer_email')) document.getElementById('buyer_email').value = email;
}
function clearBuyerData() {
    document.getElementById('whatsapp').value = '';
    if (document.getElementById('buyer_email')) document.getElementById('buyer_email').value = '';
    localStorage.removeItem('buyer_phone');
    localStorage.removeItem('buyer_email');
}
document.getElementById('whatsapp').addEventListener('input', saveBuyerData);
if (document.getElementById('buyer_email')) document.getElementById('buyer_email').addEventListener('input', saveBuyerData);
loadBuyerData();

function toggleSearch() {
    const searchBar = document.getElementById('searchBar');
    searchBar.classList.toggle('hidden');
    if (!searchBar.classList.contains('hidden')) {
        document.getElementById('headerSearchInput').focus();
    }
}

function toggleDescription() {
    const content = document.getElementById('descContent');
    const icon = document.getElementById('descIcon');
    const btnText = document.getElementById('descBtnText');
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
    btnText.textContent = content.classList.contains('hidden') ? 'Lihat Deskripsi' : 'Tutup Deskripsi';
}

function setActiveCategory(name) {
    document.querySelectorAll('.category-pill').forEach(pill => {
        pill.classList.remove('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-lg', 'shadow-slate-200');
        pill.classList.add('bg-white', 'text-slate-500', 'border-gray-200');
    });

    const activePill = document.querySelector(`.category-pill[data-category="${name}"]`);
    if (activePill) {
        activePill.classList.remove('bg-white', 'text-slate-500', 'border-gray-200');
        activePill.classList.add('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-lg', 'shadow-slate-200');
    }

    document.querySelectorAll('.variant-group').forEach(group => {
        group.classList.add('hidden');
    });

    const activeGroup = document.querySelector(`.variant-group[data-variant="${name}"]`);
    if (activeGroup) {
        activeGroup.classList.remove('hidden');
    }
}

function selectProduct(el, sku, price, name) {
    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.remove('card-selected');
        card.querySelector('.badge-check').classList.add('hidden');
    });

    el.classList.add('card-selected');
    el.querySelector('.badge-check').classList.remove('hidden');

    selectedProduct = { sku, price, name };

    document.getElementById('paymentPlaceholder').classList.add('hidden');
    document.getElementById('paymentContainer').classList.remove('hidden');

    updatePaymentPrices();
    updateBottomBar();
}

function togglePaymentGroup(btn) {
    const items = btn.nextElementSibling;
    const icon = btn.querySelector('.group-icon');
    items.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function selectPayment(el, code, fee, feeType) {
    if (!selectedProduct) {
        showPopup('Pilih produk terlebih dahulu', 'warning');
        return;
    }

    document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('card-selected');
        card.querySelector('.badge-check').classList.add('hidden');
    });

    el.classList.add('card-selected');
    el.querySelector('.badge-check').classList.remove('hidden');

    selectedPayment = { code, fee, feeType };

    updateBottomBar();
}

function updatePaymentPrices() {
    if (!selectedProduct) return;

    document.querySelectorAll('.payment-card').forEach(card => {
        const fee = parseFloat(card.getAttribute('onclick').match(/,\s*(\d+(?:\.\d+)?),/)[1]);
        const feeType = card.getAttribute('onclick').match(/'([^']+)'\)$/)[1];

        let totalFee = 0;
        if (feeType === 'percentage') {
            totalFee = selectedProduct.price * (fee / 100);
        } else {
            totalFee = fee;
        }

        const total = selectedProduct.price + totalFee;
        card.querySelector('.payment-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
    });
}

function updateBottomBar() {
    const nameEl = document.getElementById('bottomItemName');
    const priceEl = document.getElementById('bottomTotalPrice');

    if (!selectedProduct) {
        nameEl.innerHTML = '<span class="italic">Pilih produk dulu...</span>';
        priceEl.textContent = 'Rp 0';
        return;
    }

    nameEl.innerHTML = '<span class="bg-brand-accent/15 text-brand-primary px-2 py-0.5 rounded-md text-[10px] font-bold">ITEM</span> ' + selectedProduct.name;

    let total = selectedProduct.price;
    if (selectedPayment) {
        if (selectedPayment.feeType === 'percentage') {
            total += selectedProduct.price * (selectedPayment.fee / 100);
        } else {
            total += selectedPayment.fee;
        }
    }

    priceEl.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
}

function validateAccount() {
    const btn = document.getElementById('validateBtn');
    const resultDiv = document.getElementById('accountResult');
    const nicknameSpan = document.getElementById('resultNickname');

    const userId = document.getElementById('user_id')?.value || '';
    const serverId = document.getElementById('server_id')?.value || '';

    if (!userId) {
        showPopup('Masukkan User ID terlebih dahulu', 'warning');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengecek...';

    fetch('{{ route("order.validate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: userId,
            server_id: serverId,
            game_code: '{{ $category["game_code"] }}'
        })
    })
    .then(res => res.json())
    .then(data => {
        resultDiv.classList.remove('hidden');

        if (data.success) {
            resultDiv.className = 'mt-4 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3';
            resultDiv.querySelector('div:first-child').className = 'w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white';
            resultDiv.querySelector('i').className = 'fa-solid fa-check';
            nicknameSpan.textContent = data.data.nickname;
            validatedNickname = data.data.nickname;
        } else {
            resultDiv.className = 'mt-4 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3';
            resultDiv.querySelector('div:first-child').className = 'w-10 h-10 rounded-full bg-red-500 flex items-center justify-center text-white';
            resultDiv.querySelector('i').className = 'fa-solid fa-times';
            nicknameSpan.textContent = data.error || 'Akun tidak ditemukan';
            validatedNickname = null;
        }
    })
    .catch(err => {
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'mt-4 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3';
        nicknameSpan.textContent = 'Gagal mengecek akun';
        validatedNickname = null;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-search"></i> <span>Cek Akun</span>';
    });
}

function processOrder() {
    const checkId = {{ $category['check_id'] ? 'true' : 'false' }};

    if (checkId && !validatedNickname) {
        showPopup('Silakan cek akun terlebih dahulu', 'warning');
        return;
    }

    if (!selectedProduct) {
        showPopup('Pilih produk terlebih dahulu', 'warning');
        document.getElementById('section-nominal').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    if (!selectedPayment) {
        showPopup('Pilih metode pembayaran terlebih dahulu', 'warning');
        document.getElementById('section-payment').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    const whatsapp = document.getElementById('whatsapp').value;
    const buyerEmail = document.getElementById('buyer_email') ? document.getElementById('buyer_email').value : '';

    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const btnArrow = document.getElementById('btnArrow');

    btn.disabled = true;
    btnText.classList.add('hidden');
    btnArrow.classList.add('hidden');
    btnLoading.classList.remove('hidden');

    const userId = document.getElementById('user_id')?.value || '';
    const serverId = document.getElementById('server_id')?.value || '';
    const nickname = validatedNickname || userId;

    const formData = {
        product_code: '{{ $category["code"] }}',
        item_sku: selectedProduct.sku,
        item_price: selectedProduct.price,
        item_name: selectedProduct.name,
        payment_code: selectedPayment.code,
        user_id: userId,
        server_id: serverId,
        nickname: nickname,
        whatsapp: whatsapp,
        email: buyerEmail
    };

    fetch('{{ route("order.create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            showPopup(data.error || 'Gagal membuat pesanan', 'error');
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnArrow.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    })
    .catch(err => {
        showPopup('Terjadi kesalahan. Silakan coba lagi.', 'error');
        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnArrow.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    });
}
</script>
</body>
</html>
