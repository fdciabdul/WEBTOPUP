<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }} touch-effect">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>
    <a href="#" class="nav-item touch-effect">
        <i class="fas fa-gift"></i>
        <span>Promo</span>
    </a>
    <div class="nav-center-wrapper">
        <a href="{{ route('track.order') }}" class="nav-center-btn touch-effect">
            <i class="fas fa-receipt"></i>
        </a>
    </div>
    <a href="#" class="nav-item touch-effect">
        <i class="fas fa-headset"></i>
        <span>Bantuan</span>
    </a>
    <a href="#" class="nav-item touch-effect">
        <i class="fas fa-user"></i>
        <span>Akun</span>
    </a>
</nav>
