<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --brand-primary: #0033AA;
            --brand-secondary: #002280;
            --brand-accent: #3366FF;
            --bg-page: #F4F5FA;
        }

        body { font-family: 'Outfit', sans-serif; background-color: var(--bg-page); color: #1C1C1E; -webkit-tap-highlight-color: transparent; }
        h1, h2, h3, h4, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }

        @keyframes slideInUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .slide-up { animation: slideInUp 0.5s ease-out forwards; }

        @keyframes scanLine { 0% { top: 0%; opacity: 0; } 50% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
        .scan-line { position: absolute; left: 0; width: 100%; height: 4px; background: #ef4444; box-shadow: 0 0 20px #ef4444; animation: scanLine 2.5s infinite; z-index: 10; }

        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        .animate-shimmer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to right, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%); animation: shimmer 2s infinite; pointer-events: none; }

        .card-glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .bg-pattern { background-image: radial-gradient(#0033AA 0.5px, transparent 0.5px), radial-gradient(#0033AA 0.5px, #F4F5FA 0.5px); background-size: 24px 24px; opacity: 0.05; }

        .touch-effect { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .touch-effect:active { transform: scale(0.96); }
    </style>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { primary: '#0033AA', secondary: '#002280', accent: '#3366FF' } }, screens: { '3xl': '1600px' } } } }
    </script>
</head>
<body class="min-h-screen flex flex-col items-center relative overflow-x-hidden selection:bg-brand-primary/20">

    <div class="absolute inset-0 bg-pattern z-0 pointer-events-none"></div>

    <!-- Header -->
    <header class="w-full max-w-[680px] lg:max-w-[1080px] bg-brand-primary/95 backdrop-blur-xl lg:rounded-b-[32px] shadow-lg sticky top-0 z-50 transition-all duration-300 border-b border-white/10">
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
            <div class="flex items-center gap-2">
                <a href="https://wa.me/{{ config('whatsapp.fonnte.sender', '6281234567890') }}" target="_blank" class="w-9 h-9 rounded-full flex items-center justify-center text-white hover:bg-white/10 touch-effect transition relative">
                    <i class="fa-solid fa-headset text-sm"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-green-400 rounded-full border-2 border-brand-primary animate-pulse"></span>
                </a>
            </div>
        </div>
    </header>

    <div class="w-full max-w-[680px] lg:max-w-[1080px] px-3 lg:px-0 py-5 relative z-10 pb-32">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- Left Column -->
            <div class="lg:col-span-7 order-1 space-y-5">

                <!-- Timer Card -->
                <div class="bg-gradient-to-r from-blue-600 to-brand-secondary rounded-[20px] p-4 shadow-lg shadow-blue-500/30 slide-up relative overflow-hidden text-white" style="animation-delay: 0.1s;">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8"></div>
                    <div class="flex flex-row items-center justify-between gap-3 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/20">
                                <i class="fa-regular fa-clock text-base animate-[pulse_2s_infinite]"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-blue-100 uppercase tracking-wide opacity-80">Sisa Waktu Pembayaran</p>
                                <p class="text-2xl font-mono font-bold tracking-tight tabular-nums" id="countdownTimer">30:00</p>
                            </div>
                        </div>
                        <div class="bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm border border-white/10">
                            <span class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-ping"></span>
                                Menunggu
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Card -->
                <div class="card-glass rounded-[24px] p-5 slide-up relative overflow-hidden shadow-xl" style="animation-delay: 0.2s;">

                    <!-- Payment Method Header -->
                    <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 border border-gray-100 rounded-xl bg-white shadow-sm">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-6 w-auto object-contain">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Metode Pembayaran</span>
                                <span class="text-base font-bold text-slate-800 leading-none">QRIS</span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    @if($qrCodeUrl)
                    <div class="flex flex-col items-center">
                        <div class="relative group cursor-pointer w-full max-w-[220px] mx-auto mb-3">
                            <div class="bg-white p-3 rounded-[24px] border-[3px] border-slate-100 shadow-2xl shadow-slate-200/50 relative overflow-hidden">
                                <div class="scan-line"></div>
                                <img src="{{ $qrCodeUrl }}" class="w-full h-auto object-contain block mx-auto rounded-lg" alt="QRIS Code">
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="bg-white/90 backdrop-blur-sm px-2 py-1 rounded shadow-sm border border-gray-100">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-4 w-auto">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mb-3 text-center">Scan menggunakan e-wallet atau m-banking</p>
                    </div>
                    @else
                    <div class="text-center py-8 bg-slate-50 rounded-[18px] border border-dashed border-slate-200">
                        <i class="fa-solid fa-spinner fa-spin text-slate-300 text-2xl mb-2"></i>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mt-2">Memuat QR Code...</p>
                    </div>
                    @endif

                    <!-- Total Amount -->
                    <div class="bg-white rounded-[18px] p-4 border border-gray-100 flex items-center justify-between shadow-sm mt-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Tagihan</p>
                            <p class="text-lg font-bold text-brand-primary">Rp {{ number_format($invoiceData['total'] ?? $orderData['item_price'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <button onclick="copyAmount()" class="text-slate-500 hover:text-brand-primary transition font-bold text-[10px] bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 touch-effect">
                            <i class="fa-regular fa-copy mr-1"></i> Salin
                        </button>
                    </div>

                    <!-- Check Status Button -->
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <button onclick="checkPaymentStatus()" id="checkStatusBtn"
                                class="w-full bg-gradient-to-r from-brand-primary to-brand-secondary text-white font-bold py-3 rounded-xl shadow-xl shadow-brand-primary/40 hover:shadow-brand-primary/60 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group touch-effect disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none text-sm">
                            <div class="animate-shimmer"></div>
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <span id="btnText">Cek Status Pembayaran</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform text-xs"></i>
                            </span>
                        </button>
                        <p class="text-[9px] text-center text-slate-400 mt-2 font-medium flex items-center justify-center gap-1">
                            <i class="fa-solid fa-shield-halved text-green-500"></i> Transaksi Aman & Terenkripsi SSL
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-5 order-2">
                <div class="sticky top-20 space-y-4">

                    <!-- Informasi Pesanan -->
                    <div class="bg-white rounded-[20px] shadow-lg shadow-slate-200/50 overflow-hidden slide-up border border-slate-100 p-4 relative" style="animation-delay: 0.05s;">
                        <div class="flex items-center gap-2.5 mb-3 border-b border-dashed border-gray-100 pb-2.5">
                            <div class="w-7 h-7 rounded-full bg-blue-50 text-brand-primary flex items-center justify-center">
                                <i class="fa-solid fa-circle-info text-xs"></i>
                            </div>
                            <h3 class="font-heading font-bold text-slate-800 text-xs">Informasi Pesanan</h3>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-slate-500 font-medium">Status Pembayaran</span>
                                <span id="paymentStatusBadge" class="font-bold px-2 py-0.5 rounded text-[9px] uppercase tracking-wider bg-yellow-100 text-yellow-700">UNPAID</span>
                            </div>

                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-slate-500 font-medium">Status Pesanan</span>
                                <span id="orderStatusBadge" class="font-bold px-2 py-0.5 rounded text-[9px] uppercase tracking-wider bg-blue-50 text-blue-600">Queued</span>
                            </div>

                            <div id="orderMessageBox" class="bg-slate-50 rounded-lg p-2.5 border border-slate-100 mt-1.5">
                                <p class="text-[10px] leading-relaxed font-medium text-slate-600">
                                    <i id="orderMessageIcon" class="fa-solid fa-hourglass-half text-blue-500 mr-1"></i>
                                    <span id="orderMessageText">Pesananmu sedang dalam proses antrian.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-white rounded-[20px] shadow-lg shadow-slate-200/50 overflow-hidden slide-up border border-slate-100" style="animation-delay: 0.3s;">
                        <div class="p-4 cursor-pointer hover:bg-slate-50 transition relative group select-none">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white rounded-lg p-1.5 shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                                    <img src="https://cdn-icons-png.flaticon.com/512/214/214305.png" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-heading font-bold text-slate-800 text-sm leading-tight mb-0.5">{{ $orderData['item_name'] ?? 'Product' }}</h3>
                                    <p class="font-extrabold text-brand-primary text-base leading-tight">Rp {{ number_format($invoiceData['total'] ?? $orderData['item_price'] ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 border-t border-slate-100 p-4 space-y-3">
                            @if($invoiceNumber)
                            <div class="flex justify-between items-center text-[11px] group py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">No. Invoice</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 font-mono">{{ $invoiceNumber }}</span>
                                    <button onclick="copyText('{{ $invoiceNumber }}')" class="text-brand-primary hover:text-brand-secondary transition"><i class="fa-regular fa-copy"></i></button>
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-between items-center text-[11px] group py-1 border-b border-dashed border-gray-200">
                                <span class="text-slate-500 font-medium">User ID</span>
                                <span class="font-bold text-slate-800">{{ $orderData['game_id'] ?? '-' }}</span>
                            </div>

                            <div class="flex justify-between items-center text-[11px] group py-1">
                                <span class="text-slate-500 font-medium">Nickname</span>
                                <span class="font-bold text-slate-800">{{ $orderData['nickname'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <a href="https://wa.me/{{ config('whatsapp.fonnte.sender', '6281234567890') }}" target="_blank" class="block bg-gradient-to-br from-brand-secondary to-brand-primary rounded-[20px] p-4 text-white shadow-lg relative overflow-hidden group cursor-pointer touch-effect">
                        <div class="absolute right-0 top-0 w-20 h-20 bg-white/10 rounded-full blur-xl -mr-6 -mt-6 group-hover:bg-white/20 transition"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white">
                                <i class="fa-solid fa-headset text-base"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-blue-100 font-medium">Butuh Bantuan?</p>
                                <p class="text-xs font-bold">Hubungi CS {{ config('app.name') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-white/50 group-hover:text-white transition text-xs"></i>
                        </div>
                    </a>

                    @if($invoiceNumber)
                    <a href="{{ route('order.status', ['invoice' => $invoiceNumber]) }}" class="block bg-white rounded-[20px] p-4 shadow-lg border border-slate-100 text-slate-700 touch-effect hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center">
                                <i class="fa-solid fa-receipt text-brand-primary text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Lihat Status Order</p>
                                <p class="text-[10px] text-slate-400">Cek progres pesanan Anda</p>
                            </div>
                            <i class="fa-solid fa-chevron-right ml-auto text-slate-400 text-xs"></i>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div id="toast" class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[100] w-auto max-w-[90%] hidden">
            <div class="bg-slate-900/95 backdrop-blur-md text-white pl-4 pr-6 py-3 rounded-full shadow-2xl flex items-center gap-3 border border-white/10 ring-2 ring-white/20">
                <div class="rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-lg animate-bounce bg-emerald-500 shadow-emerald-500/30">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span class="font-bold text-sm tracking-wide" id="toastMessage">Berhasil disalin!</span>
            </div>
        </div>

    </div>

    <script>
    // Countdown timer (30 minutes)
    let timeLeft = 30 * 60;
    const timerEl = document.getElementById('countdownTimer');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        if (timeLeft <= 0) {
            timerEl.textContent = 'Expired';
            // Update status badges to expired
            updateStatusBadges('expired', 'Expired');
        } else {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }
    }
    updateTimer();

    // Toast notification
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }

    // Copy functions
    function copyText(text) {
        navigator.clipboard.writeText(text);
        showToast('Berhasil disalin!');
    }

    function copyAmount() {
        const amount = '{{ $invoiceData['total'] ?? $orderData['item_price'] ?? 0 }}';
        navigator.clipboard.writeText(amount);
        showToast('Jumlah berhasil disalin!');
    }

    // Update status badges
    function updateStatusBadges(paymentStatus, orderStatus) {
        const paymentBadge = document.getElementById('paymentStatusBadge');
        const orderBadge = document.getElementById('orderStatusBadge');
        const orderMessageIcon = document.getElementById('orderMessageIcon');
        const orderMessageText = document.getElementById('orderMessageText');

        // Payment status styling
        paymentBadge.textContent = paymentStatus.toUpperCase();
        paymentBadge.className = 'font-bold px-2 py-0.5 rounded text-[9px] uppercase tracking-wider ';
        if (paymentStatus.toLowerCase() === 'paid' || paymentStatus.toLowerCase() === 'success') {
            paymentBadge.className += 'bg-green-100 text-green-700';
            paymentBadge.textContent = 'PAID';
        } else if (paymentStatus.toLowerCase() === 'expired') {
            paymentBadge.className += 'bg-red-100 text-red-700';
        } else {
            paymentBadge.className += 'bg-yellow-100 text-yellow-700';
        }

        // Order status styling
        orderBadge.textContent = orderStatus;
        orderBadge.className = 'font-bold px-2 py-0.5 rounded text-[9px] uppercase tracking-wider ';

        const statusMessages = {
            'Queued': { icon: 'fa-hourglass-half text-blue-500', msg: 'Pesananmu sedang dalam proses antrian.', class: 'bg-blue-50 text-blue-600' },
            'Proses': { icon: 'fa-gears text-indigo-500', msg: 'Pesananmu sedang diproses.', class: 'bg-indigo-50 text-indigo-600' },
            'Done': { icon: 'fa-circle-check text-green-500', msg: 'Pesananmu telah selesai diproses.', class: 'bg-green-50 text-green-600' },
            'Cancel': { icon: 'fa-ban text-red-500', msg: 'Pesananmu telah dibatalkan oleh sistem.', class: 'bg-red-50 text-red-600' },
            'Expired': { icon: 'fa-clock text-gray-500', msg: 'Waktu pembayaranmu telah habis.', class: 'bg-gray-100 text-gray-600' }
        };

        const statusInfo = statusMessages[orderStatus] || statusMessages['Queued'];
        orderBadge.className += statusInfo.class;
        orderMessageIcon.className = 'fa-solid ' + statusInfo.icon + ' mr-1';
        orderMessageText.textContent = statusInfo.msg;
    }

    // Check payment status
    function checkPaymentStatus() {
        const invoiceNumber = '{{ $invoiceNumber ?? "" }}';
        if (!invoiceNumber) return;

        const btn = document.getElementById('checkStatusBtn');
        const btnText = document.getElementById('btnText');
        btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengecek...';
        btn.disabled = true;

        fetch('{{ route("order.track") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ invoice: invoiceNumber })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const paymentStatus = data.data.statusPayment || data.data.status_payment || 'pending';
                const orderStatus = data.data.statusOrder || data.data.status || 'Queued';

                // Update the status badges
                updateStatusBadges(paymentStatus, orderStatus);

                if (paymentStatus.toLowerCase() === 'success' || paymentStatus.toLowerCase() === 'paid') {
                    window.location.href = '{{ route("order.status") }}?invoice=' + invoiceNumber;
                } else {
                    showToast('Pembayaran belum diterima');
                }
            }
        })
        .catch(err => console.error('Status check error:', err))
        .finally(() => {
            btnText.textContent = 'Cek Status Pembayaran';
            btn.disabled = false;
        });
    }

    // Auto check every 10 seconds
    setInterval(checkPaymentStatus, 10000);
    </script>
</body>
</html>
