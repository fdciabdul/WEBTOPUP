<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#EEF4FF', 100: '#D9E6FF', 200: '#BCD4FF', 300: '#8EB6FF',
                            400: '#5990FF', 500: '#0033AA', 600: '#002B88', 700: '#002685',
                            800: '#001F66', 900: '#001445'
                        }
                    },
                    animation: {
                        'blob': 'blob 10s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up-fade': 'slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' }
                        },
                        slideUpFade: {
                            '0%': { opacity: 0, transform: 'translateY(30px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #F8FAFC; overflow-x: hidden; }

        .bg-blobs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: #F8FAFC; }
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4; }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }

        .card-anim {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative; overflow: hidden;
        }
        .card-anim:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -5px rgba(0, 51, 170, 0.15);
            z-index: 10;
        }

        .nav-item {
            display: flex; align-items: center; gap: 0.8rem; padding: 0.6rem 1rem;
            border-radius: 0.8rem; font-weight: 600; font-size: 0.9rem;
            color: #64748b; margin-bottom: 0.2rem;
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), background 0.3s, color 0.3s;
        }
        .nav-item:hover { background: rgba(255,255,255,0.8); color: #0033AA; transform: translateX(5px); }
        .nav-item:active { transform: scale(0.95) translateX(5px); }
        .nav-active {
            background-color: #0033AA;
            color: white !important;
            box-shadow: 0 8px 20px -5px rgba(0, 51, 170, 0.3);
        }
        .nav-active:hover { background-color: #0033AA; color: white !important; transform: none; }

        .nav-section {
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
            color: #94a3b8; margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 0.5rem;
        }

        .table-header-gradient { background: #0033AA; color: white; }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #0033AA; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

        /* Unified section card headers — all forced to brand blue */
        .header-blue, .header-pink, .header-purple, .header-orange, .header-teal, .header-slate,
        .header-solid { background: linear-gradient(135deg, #0033AA, #002288) !important; color: white !important; }

        /* Input focus styles */
        input:focus, select:focus, textarea:focus {
            border-color: #0033AA !important;
            box-shadow: 0 0 0 3px rgba(0, 51, 170, 0.1);
        }

        /* Toggle checkbox brand color */
        input[type="checkbox"]:checked { background-color: #0033AA; border-color: #0033AA; }

        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }

        .badge-status {
            padding: 0.375rem 0.75rem; border-radius: 9999px;
            font-size: 0.625rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
            display: inline-flex; align-items: center; gap: 0.375rem;
        }
        .badge-success { background: #D1FAE5; color: #065F46; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        .badge-info { background: #DBEAFE; color: #1E40AF; }
        .badge-secondary { background: #E2E8F0; color: #475569; }
    </style>

    @stack('styles')
</head>
<body class="antialiased selection:bg-brand-600 selection:text-white text-slate-800">

<div class="bg-blobs">
    <div class="blob bg-blue-100 w-[500px] h-[500px] top-[-10%] left-[-10%]"></div>
    <div class="blob bg-slate-200 w-[400px] h-[400px] bottom-[-10%] right-[-10%]"></div>
</div>

<div class="flex h-screen overflow-hidden w-full font-sans">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-[280px] sm:w-72 glass-panel transition-transform duration-300 -translate-x-full lg:translate-x-0 flex flex-col flex-shrink-0 h-full border-r border-white/50">

        <div class="h-24 flex items-center px-8 flex-shrink-0 border-b border-slate-100/50">
            <div class="flex items-center gap-3 animate-slide-up-fade">
                <div class="w-10 h-10 bg-[#0033AA] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
                    <i class="ri-rocket-2-fill text-xl"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-xl text-slate-800 tracking-tight">{{ config('app.name') }}</h1>
                    <span class="text-[10px] font-bold text-white bg-brand-600 px-2 py-0.5 rounded shadow-sm">ADMIN PANEL</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-6 py-4 custom-scrollbar flex flex-col pb-20">

            <p class="nav-section animate-slide-up-fade">Overview</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }} animate-slide-up-fade delay-100">
                <i class="ri-layout-grid-fill text-lg"></i> Dashboard
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="nav-item {{ request()->routeIs('admin.transactions.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-100">
                <i class="ri-file-text-line text-lg"></i> Transaksi
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-100">
                <i class="ri-shopping-bag-3-line text-lg"></i> Produk
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-100">
                <i class="ri-list-check text-lg"></i> Kategori
            </a>
            <a href="{{ route('admin.members.index') }}" class="nav-item {{ request()->routeIs('admin.members.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-100">
                <i class="ri-user-smile-line text-lg"></i> Member
            </a>

            <p class="nav-section animate-slide-up-fade delay-200">Content</p>
            <a href="{{ route('admin.content.index') }}" class="nav-item {{ request()->routeIs('admin.content.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-200">
                <i class="ri-star-smile-line text-lg"></i> Ulasan
            </a>
            <a href="{{ route('admin.content.index') }}#faq" class="nav-item {{ request()->routeIs('admin.content.*') && request()->get('tab') === 'faq' ? 'nav-active' : '' }} animate-slide-up-fade delay-200">
                <i class="ri-question-answer-line text-lg"></i> FAQ
            </a>
            <a href="{{ route('admin.content.index') }}#bonus-files" class="nav-item {{ request()->routeIs('admin.content.*') && request()->get('tab') === 'files' ? 'nav-active' : '' }} animate-slide-up-fade delay-200">
                <i class="ri-gift-2-line text-lg"></i> Bonus File
            </a>
            <a href="{{ route('admin.content.index') }}#media" class="nav-item {{ request()->routeIs('admin.content.*') && request()->get('tab') === 'media' ? 'nav-active' : '' }} animate-slide-up-fade delay-200">
                <i class="ri-newspaper-line text-lg"></i> Media Liputan
            </a>
            <a href="{{ route('admin.content.index') }}#pages" class="nav-item {{ request()->routeIs('admin.content.*') && request()->get('tab') === 'pages' ? 'nav-active' : '' }} animate-slide-up-fade delay-200">
                <i class="ri-information-line text-lg"></i> Informasi
            </a>

            <p class="nav-section animate-slide-up-fade delay-300">System</p>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-settings-4-line text-lg"></i> Setting
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'payment']) }}" class="nav-item {{ request()->routeIs('admin.settings.*') && request('tab') === 'payment' ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-bank-card-line text-lg"></i> Payment Gateway
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'providers']) }}" class="nav-item {{ request()->routeIs('admin.settings.*') && request('tab') === 'providers' ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-gamepad-line text-lg"></i> API Games
            </a>
            <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-notification-3-line text-lg"></i> Notifikasi
            </a>
            <a href="{{ route('admin.security.index') }}" class="nav-item {{ request()->routeIs('admin.security.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-shield-check-line text-lg"></i> Keamanan
            </a>
            <a href="{{ route('admin.auto-delete.index') }}" class="nav-item {{ request()->routeIs('admin.auto-delete.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-delete-bin-2-line text-lg"></i> Auto Delete
            </a>
            <a href="{{ route('admin.activity-logs.index') }}" class="nav-item {{ request()->routeIs('admin.activity-logs.*') ? 'nav-active' : '' }} animate-slide-up-fade delay-300">
                <i class="ri-history-line text-lg"></i> Log Activity
            </a>
            <a href="{{ route('home') }}" target="_blank" class="nav-item animate-slide-up-fade delay-300">
                <i class="ri-global-line text-lg"></i> Lihat Website
            </a>

            <div class="mt-8 mb-4 border-t border-slate-200/60 pt-4 animate-slide-up-fade delay-400">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item text-red-500 hover:text-red-600 hover:bg-red-50 w-full">
                        <i class="ri-logout-box-line text-lg"></i> Sign Out
                    </button>
                </form>
            </div>

        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen relative overflow-hidden w-full">

        <!-- Header -->
        <header class="h-20 sm:h-24 glass-panel px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 flex-shrink-0 m-2 sm:m-4 rounded-2xl sm:rounded-3xl border-none">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
                    <i class="ri-menu-2-line text-xl"></i>
                </button>
                <div class="hidden md:block animate-slide-up-fade">
                    <div class="flex items-center gap-2 text-slate-400 font-bold text-sm">
                        <span>Pages</span> <i class="ri-arrow-right-s-line text-xs"></i> <span class="text-brand-600">@yield('page-title', 'Dashboard')</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4 animate-slide-up-fade delay-100">
                    <a href="{{ route('home') }}" target="_blank" class="w-11 h-11 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-600 hover:text-brand-600 hover:shadow-md transition-all">
                        <i class="ri-external-link-line text-xl"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0033AA&color=fff" class="w-11 h-11 rounded-full shadow-md border-2 border-white">
                        <div class="hidden sm:block">
                            <p class="font-bold text-slate-800 text-sm">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-500">{{ auth()->user()->role ?? 'Administrator' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-8 pt-0 space-y-4 sm:space-y-6 md:space-y-8 scroll-smooth custom-scrollbar">

            @if(session('success'))
            <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 border-l-4 border-green-500 animate-slide-up-fade">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                </div>
                <span class="font-medium text-slate-700">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 border-l-4 border-red-500 animate-slide-up-fade">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                    <i class="ri-error-warning-fill text-xl"></i>
                </div>
                <span class="font-medium text-slate-700">{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="glass-panel rounded-2xl p-4 border-l-4 border-red-500 animate-slide-up-fade">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                        <i class="ri-error-warning-fill text-xl"></i>
                    </div>
                    <span class="font-bold text-slate-800">Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-sm text-slate-600 ml-12 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')

        </div>
    </main>
</div>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

@stack('scripts')
</body>
</html>
