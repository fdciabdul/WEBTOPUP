<div class="header">
    <div class="header-top">
        <div class="user-info">
            <h1>
                @auth
                    Welcome, {{ auth()->user()->name }}! 👋
                @else
                    Welcome to {{ config('app.name') }}! 👋
                @endauth
            </h1>
            <p>
                @auth
                    @if(auth()->user()->isAdmin())
                        Administrator
                    @else
                        Level: {{ ucfirst(str_replace('_', ' ', auth()->user()->level)) }}
                    @endif
                @else
                    Guest User
                @endauth
            </p>
        </div>
        <div class="action-icons">
            @guest
                <a href="{{ route('login') }}" class="auth-btn-mobile touch-effect bounce-trigger">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
                <a href="{{ route('login') }}" class="auth-btn bounce-trigger">
                    <i class="fas fa-sign-in-alt"></i> Login / Daftar
                </a>
            @else
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard.index') }}" class="auth-btn bounce-trigger">
                    <i class="fas fa-user"></i> Dashboard
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="auth-btn bounce-trigger" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            @endguest
        </div>
    </div>

    <div class="search-row">
        <div class="search-container">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Temukan produk..." value="{{ request('q') }}">
                <i class="fas fa-search search-icon"></i>
            </form>
        </div>
        @auth
            <a href="{{ route('track.order') }}" class="filter-btn touch-effect bounce-trigger">
                <i class="fas fa-history"></i>
            </a>
        @endauth
    </div>
</div>
