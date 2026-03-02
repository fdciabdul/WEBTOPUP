<!-- Header (Fixed) -->
<div class="header">
    <div class="header-top">
        <div class="user-info">
            <h1>{{ config('app.name') }}</h1>
            <p>Top Up Game Cepat & Murah</p>
        </div>

        <!-- Desktop Navigation (hidden on mobile) -->
        <nav class="desktop-nav">
            <a href="{{ route('home') }}" class="nav-link-desktop {{ request()->routeIs('home') ? 'active' : '' }} touch-effect">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
            <a href="#" class="nav-link-desktop touch-effect">
                <i class="fas fa-gift"></i>
                <span>Promo</span>
            </a>
            <a href="{{ route('track.order') }}" class="nav-link-desktop {{ request()->routeIs('track.order') ? 'active' : '' }} touch-effect">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
            </a>
            <a href="#" class="nav-link-desktop touch-effect">
                <i class="fas fa-headset"></i>
                <span>Bantuan</span>
            </a>
            <a href="#" class="nav-link-desktop touch-effect">
                <i class="fas fa-user"></i>
                <span>Akun</span>
            </a>
        </nav>

        <div class="action-icons">
            <a href="{{ route('track.order') }}" class="auth-btn-mobile touch-effect">
                <i class="fas fa-history"></i>
                <span>Riwayat</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-row">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <form action="{{ route('search') }}" method="GET" style="width: 100%;">
                <input type="text" name="q" class="search-input" id="searchInput" placeholder="Cari game favorit kamu..." autocomplete="off" value="{{ request('q') }}">
            </form>
        </div>
        <div class="filter-btn touch-effect" onclick="document.querySelector('.category-wrapper')?.scrollIntoView({behavior: 'smooth', block: 'nearest'})">
            <i class="fas fa-filter"></i>
        </div>
    </div>

    @yield('header-extra')
</div>
