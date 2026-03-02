@auth
<nav class="bottom-nav">
    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('track.order') }}" class="nav-item {{ request()->routeIs('track.order') ? 'active' : '' }}">
        <i class="fas fa-search"></i>
        <span>Cek Order</span>
    </a>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-folder"></i>
            <span>Kategori</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>Produk</span>
        </a>
        <a href="{{ route('admin.transactions.index') }}" class="nav-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Transaksi</span>
        </a>
        <a href="{{ route('admin.members.index') }}" class="nav-item {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Member</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    @else
        <a href="{{ route('dashboard.index') }}" class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <a href="{{ route('dashboard.transactions') }}" class="nav-item {{ request()->routeIs('dashboard.transactions') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Transaksi</span>
        </a>
    @endif
</nav>

<style>
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: var(--app-width, 480px);
    background: white;
    display: flex;
    gap: 4px;
    padding: 12px 8px calc(12px + env(safe-area-inset-bottom));
    box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
    z-index: 999;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}
.bottom-nav::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}
.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    color: #64748B;
    text-decoration: none;
    font-size: 11px;
    transition: all 0.3s;
    padding: 8px 12px;
    border-radius: 12px;
    white-space: nowrap;
    flex-shrink: 0;
    min-width: 70px;
}
.nav-item i {
    font-size: 18px;
}
.nav-item.active {
    color: #0033AA;
    background: rgba(0,51,170,0.1);
}
.nav-item:hover {
    color: #0033AA;
}
</style>
@endauth
