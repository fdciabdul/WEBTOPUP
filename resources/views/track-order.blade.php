<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cek Pesanan - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { DEFAULT: '#0033AA', dark: '#002288', light: '#e0f2fe' },
                    },
                    boxShadow: {
                        'ios': '0 10px 40px -10px rgba(0, 122, 255, 0.3)',
                        'card': '0 12px 35px -5px rgba(0, 0, 0, 0.06)',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'spring-up': 'springUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
                        springUp: {
                            '0%': { transform: 'scale(0.8) translateY(20px)', opacity: 0 },
                            '100%': { transform: 'scale(1) translateY(0)', opacity: 1 }
                        },
                        fadeInUp: {
                            '0%': { opacity: 0, transform: 'translateY(20px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #F8FAFC;
            background-image:
                radial-gradient(at 0% 0%, hsla(210,100%,96%,1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(200,100%,96%,1) 0, transparent 50%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="antialiased text-slate-800 min-h-screen flex items-center justify-center p-4 overflow-x-hidden relative">

    <div class="w-full max-w-4xl flex flex-col items-center justify-center">

        <!-- Notification Toast -->
        <div id="notification" class="fixed top-6 z-[100] px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 font-semibold text-sm border backdrop-blur-md transition-all duration-300 transform scale-0 opacity-0">
            <i class="fa-solid fa-circle-info"></i>
            <span id="notificationText"></span>
        </div>

        <div class="w-full flex flex-col items-center transition-all duration-700 ease-in-out animate-fade-in-up">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="group relative mb-8 animate-float select-none cursor-pointer">
                <div class="absolute inset-0 bg-blue-500/30 rounded-[2.5rem] blur-2xl opacity-50 group-hover:opacity-70 transition-opacity duration-500"></div>
                <div class="relative w-24 h-24 bg-white rounded-[2rem] shadow-ios flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-500 overflow-hidden">
                    <span class="text-3xl font-extrabold text-brand">{{ substr(config('app.name'), 0, 1) }}</span>
                </div>
            </a>

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-2 tracking-tight drop-shadow-sm">Cek Status Pesanan</h1>
                <p class="text-slate-500 font-medium max-w-sm mx-auto text-sm">Masukkan Order ID untuk melacak pesanan Anda.</p>
            </div>

            <!-- Rate Limit Indicator -->
            <div id="quotaIndicator" class="mb-4 flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full shadow-sm border border-slate-100 text-sm font-medium text-slate-600 transition-all duration-300">
                <i class="fa-solid fa-shield-halved text-brand"></i>
                <span>Sisa Kuota: <strong id="quotaCount" class="text-brand">5</strong>/5</span>
                <span id="cooldownText" class="text-orange-500 hidden"></span>
            </div>

            <!-- Search Form -->
            <form id="trackForm" action="{{ route('check.order') }}" method="POST" class="w-full max-w-xl relative group transition-all duration-300 hover:-translate-y-1">
                @csrf
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-brand transition-colors text-base"></i>
                </div>

                <input
                    type="text"
                    name="order_id"
                    value="{{ old('order_id') }}"
                    class="block w-full pl-12 pr-28 py-4 bg-white border-2 border-transparent focus:border-brand-light rounded-full text-base font-medium text-slate-800 placeholder-slate-400 shadow-card focus:shadow-ios focus:outline-none focus:ring-4 focus:ring-brand-light/50 transition-all duration-300 uppercase transform focus:scale-[1.01]"
                    placeholder="ORD-20260113-XXX"
                    required
                >

                <button
                    id="submitBtn"
                    type="submit"
                    class="absolute right-2 top-2 bottom-2 bg-slate-900 hover:bg-brand text-white font-bold rounded-full px-6 transition-all duration-300 shadow-lg flex items-center gap-2 transform active:scale-95 hover:shadow-brand/50"
                >
                    <span id="btnText">Cek</span>
                </button>
            </form>

            @if(session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
            @endif

            @error('order_id')
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </div>
            @enderror

        </div>

    </div>

<script>
(function() {
    const MAX_CHECKS = 5;
    const WINDOW_MS = 60000; // 1 minute
    const STORAGE_KEY = 'track_order_timestamps';

    const form = document.getElementById('trackForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const quotaCount = document.getElementById('quotaCount');
    const quotaIndicator = document.getElementById('quotaIndicator');
    const cooldownText = document.getElementById('cooldownText');
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notificationText');

    let countdownInterval = null;

    function getTimestamps() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return [];
            return JSON.parse(raw).filter(ts => Date.now() - ts < WINDOW_MS);
        } catch { return []; }
    }

    function saveTimestamps(ts) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(ts));
    }

    function getRemaining() {
        const ts = getTimestamps();
        saveTimestamps(ts); // clean expired
        return MAX_CHECKS - ts.length;
    }

    function getEarliestExpiry() {
        const ts = getTimestamps();
        if (ts.length === 0) return 0;
        const oldest = Math.min(...ts);
        return Math.max(0, WINDOW_MS - (Date.now() - oldest));
    }

    function updateUI() {
        const remaining = getRemaining();
        quotaCount.textContent = remaining;

        if (remaining <= 0) {
            // Limit hit
            submitBtn.disabled = true;
            submitBtn.classList.remove('bg-slate-900', 'hover:bg-brand', 'hover:shadow-brand/50');
            submitBtn.classList.add('bg-slate-400', 'cursor-not-allowed');
            quotaCount.classList.remove('text-brand');
            quotaCount.classList.add('text-red-500');
            quotaIndicator.classList.add('border-red-200', 'bg-red-50/80');
            quotaIndicator.classList.remove('border-slate-100', 'bg-white/80');
            startCooldown();
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.add('bg-slate-900', 'hover:bg-brand', 'hover:shadow-brand/50');
            submitBtn.classList.remove('bg-slate-400', 'cursor-not-allowed');
            btnText.textContent = 'Cek';

            if (remaining <= 2) {
                quotaCount.classList.remove('text-brand');
                quotaCount.classList.add('text-orange-500');
                quotaIndicator.classList.remove('border-red-200', 'bg-red-50/80');
                quotaIndicator.classList.add('border-orange-200', 'bg-orange-50/80');
                quotaIndicator.classList.remove('border-slate-100', 'bg-white/80');
            } else {
                quotaCount.classList.add('text-brand');
                quotaCount.classList.remove('text-orange-500', 'text-red-500');
                quotaIndicator.classList.remove('border-red-200', 'bg-red-50/80', 'border-orange-200', 'bg-orange-50/80');
                quotaIndicator.classList.add('border-slate-100', 'bg-white/80');
            }

            cooldownText.classList.add('hidden');
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        }
    }

    function startCooldown() {
        if (countdownInterval) clearInterval(countdownInterval);

        function tick() {
            const ms = getEarliestExpiry();
            if (ms <= 0) {
                updateUI();
                return;
            }
            const secs = Math.ceil(ms / 1000);
            btnText.textContent = secs + 's';
            cooldownText.textContent = '· Tunggu ' + secs + 's';
            cooldownText.classList.remove('hidden');
        }

        tick();
        countdownInterval = setInterval(tick, 1000);
    }

    function showNotification(message, type) {
        notification.className = 'fixed top-6 z-[100] px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 font-semibold text-sm border backdrop-blur-md transition-all duration-300 transform';

        if (type === 'warning') {
            notification.classList.add('bg-orange-50', 'border-orange-200', 'text-orange-700');
            notification.querySelector('i').className = 'fa-solid fa-triangle-exclamation';
        } else if (type === 'error') {
            notification.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
            notification.querySelector('i').className = 'fa-solid fa-circle-exclamation';
        }

        notificationText.textContent = message;
        notification.classList.remove('scale-0', 'opacity-0');
        notification.classList.add('scale-100', 'opacity-100');

        setTimeout(function() {
            notification.classList.add('scale-0', 'opacity-0');
            notification.classList.remove('scale-100', 'opacity-100');
        }, 3000);
    }

    form.addEventListener('submit', function(e) {
        const remaining = getRemaining();

        if (remaining <= 0) {
            e.preventDefault();
            const secs = Math.ceil(getEarliestExpiry() / 1000);
            showNotification('Kuota habis! Tunggu ' + secs + ' detik lagi.', 'error');
            return;
        }

        // Record this check
        const ts = getTimestamps();
        ts.push(Date.now());
        saveTimestamps(ts);

        if (remaining - 1 <= 2 && remaining - 1 > 0) {
            showNotification('Sisa kuota: ' + (remaining - 1) + '/' + MAX_CHECKS, 'warning');
        }

        updateUI();
    });

    // Init
    updateUI();
})();
</script>
</body>
</html>
