{{-- resources/views/layouts/partials/navbar.blade.php --}}

<header class="site-header">

    {{-- ── TOP BAR: Logo + Icons ── --}}
    <div class="header-top">
        <div class="container">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="site-logo">
                <span class="logo-text">PhoneBy<strong>Ashahi</strong></span>
            </a>

            {{-- Right icons --}}
            <div class="header-icons">

                {{-- Language / globe --}}
                <div class="icon-link dropdown-trigger" title="Language">
                    <i class="fa-solid fa-globe"></i>
                    <i class="fa-solid fa-chevron-down chevron-sm"></i>
                </div>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist.index') }}" class="icon-link" title="Wishlist">
                    <i class="fa-regular fa-heart"></i>
                </a>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="icon-link position-relative" title="Cart">
                    <i class="fa-solid fa-bag-shopping"></i>
                    @if($cartCount ?? 0)
                        <span class="icon-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                {{-- Account --}}
                @guest
                    <a href="{{ route('auth.login') }}" class="icon-link" title="Sign in">
                        <i class="fa-regular fa-user"></i>
                    </a>
                @else
                    <div class="icon-link dropdown-trigger" title="Account">
                        <i class="fa-regular fa-user"></i>
                        <div class="dropdown-menu">
                            <a href="{{ route('profile.show') }}"><i class="fa-regular fa-user"></i> Profile</a>
                            <a href="{{ route('orders.index') }}"><i class="fa-solid fa-bag-shopping"></i> My Orders</a>
                            <a href="{{ route('wishlist.index') }}"><i class="fa-regular fa-heart"></i> Wishlist</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('nav-logout-form').submit();">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                    <form id="nav-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf
                    </form>
                @endguest

            </div>

        </div>
    </div>

    {{-- ── BOTTOM BAR: Search + Category nav ── --}}
    <nav class="header-nav">
        <div class="container">

            {{-- Search --}}
            <form action="{{ route('products.index') }}" method="GET" class="header-search">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search Product ..."
                    class="search-input" autocomplete="off">
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <ul class="nav-list">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li class="has-mega">
                    <a href="{{ route('products.index') }}"
                        class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        Phone
                    </a>
                    <div class="mega-menu">
                        <div class="mega-col">
                            <div class="mega-label">By Brand</div>
                            @foreach(\App\Models\Brand::take(8)->get() as $brand)
                                <a href="{{ route('products.index', ['brand' => $brand->id]) }}">{{ $brand->name }}</a>
                            @endforeach
                        </div>
                        <div class="mega-col">
                            <div class="mega-label">By Category</div>
                            @foreach(\App\Models\Category::take(8)->get() as $cat)
                                <a href="{{ route('products.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                        <div class="mega-col">
                            <div class="mega-label">Shop By</div>
                            <a href="{{ route('products.index', ['sort' => 'newest']) }}">New Arrivals</a>
                            <a href="{{ route('products.index', ['sort' => 'bestseller']) }}">Best Sellers</a>
                            <a href="{{ route('products.index', ['sale' => 1]) }}">On Sale</a>
                        </div>
                    </div>
                </li>
                <li><a href="{{ route('products.index', ['category' => 'accessories']) }}">Accessories</a></li>
                <li><a href="{{ route('contact.index') }}">Contact Us</a></li>
                <li><a href="{{ route('orders.index') }}">My Orders</a></li>
            </ul>

            {{-- Mobile hamburger --}}
            <button class="hamburger" id="hamburger" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

</header>

<style>
    :root {
        --clr-navbar-blue: #ffffff;
    }

    /* ── HEADER TOP (white) ── */
    .site-header {
        position: sticky;
        top: 0;
        z-index: 200;
        background: var(--clr-white);
        box-shadow: var(--shadow-md);
    }

    .header-top {
        background: var(--clr-white);
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .header-top .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Logo */
    .site-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .logo-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--clr-navbar-blue);
        color: #fff;
        display: grid;
        place-items: center;
        font-size: 18px;
    }

    /* Icons (top bar) */
    .header-icons {
        display: flex;
        align-items: center;
        gap: 22px;
    }

    .icon-link {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 19px;
        color: var(--clr-dark);
        position: relative;
        cursor: pointer;
        transition: color var(--ease);
        text-decoration: none;
    }

    .icon-link:hover {
        color: var(--clr-navbar-blue);
    }

    .chevron-sm {
        font-size: 10px;
    }

    .icon-badge {
        position: absolute;
        top: -7px;
        right: -9px;
        min-width: 16px;
        height: 16px;
        background: var(--clr-danger);
        color: #ff0000;
        border-radius: 50%;
        font-size: 9px;
        font-weight: 700;
        display: grid;
        place-items: center;
        border: 2px solid var(--clr-white);
    }

    /* Account dropdown */
    .dropdown-trigger {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        background: var(--clr-white);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        min-width: 180px;
        padding: 8px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: all var(--ease);
        z-index: 300;
    }

    .dropdown-trigger:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .logo-text {
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 500;
        color: var(--clr-dark);
        letter-spacing: -0.5px;
    }

    .logo-text strong {
        color: var(--clr-primary);
        font-weight: 700;
    }

    .dropdown-menu a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        font-size: 13.5px;
        color: var(--clr-dark);
    }

    .dropdown-menu a:hover {
        background: var(--clr-surface);
        color: var(--clr-navbar-blue);
    }

    .dropdown-divider {
        height: 1px;
        background: var(--clr-border);
        margin: 4px 0;
    }

    /* ── HEADER NAV (blue bottom bar) ── */
    .header-nav {
        background: var(--clr-navbar-blue);
        padding: 10px 0;
    }

    .header-nav .container {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    /* Search */
    .header-search {
        flex: 1;
        display: flex;
        background: rgb(249, 247, 247);
        border-radius: var(--radius-pill);
        overflow: hidden;
        max-width: 600px;
        border: 1px solid #ddd;
    }

    .search-input {
        flex: 1;
        padding: 9px 18px;
        border: none;
        outline: none;
        font-size: 13.5px;
        font-family: var(--font-body);
        color: #333;
    }

    .search-input::placeholder {
        color: #666;
    }

    .search-btn {
        padding: 0 18px;
        background: transparent;
        color: #666;
        border: none;
        font-size: 14px;
        cursor: pointer;
        transition: color var(--ease);
    }

    .search-btn:hover {
        color: #10336b;
    }

    /* Nav list */
    .nav-list {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }

    .nav-list>li {
        position: relative;
    }

    .nav-list>li>a {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 14px;
        font-size: 14.5px;
        font-weight: 700;
        color: #10336b;
        transition: opacity var(--ease);
    }

    .nav-list>li>a:hover,
    .nav-list>li>a.active {
        opacity: 0.8;
    }

    /* Mega menu */
    .mega-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--clr-white);
        border: 1px solid var(--clr-border);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        box-shadow: var(--shadow-lg);
        padding: 20px;
        display: flex;
        gap: 32px;
        min-width: 420px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: all var(--ease);
        z-index: 300;
        margin-top: 10px;
    }

    .has-mega:hover .mega-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .mega-col {
        min-width: 120px;
    }

    .mega-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
        color: var(--clr-muted);
        margin-bottom: 10px;
    }

    .mega-col a {
        display: block;
        padding: 5px 0;
        font-size: 13.5px;
        color: var(--clr-dark);
        font-weight: 400;
    }

    .mega-col a:hover {
        color: var(--clr-navbar-blue);
        padding-left: 4px;
    }

    /* Hamburger */
    .hamburger {
        display: none;
        flex-direction: column;
        gap: 5px;
        padding: 8px;
        background: none;
        border: none;
        cursor: pointer;
    }

    .hamburger span {
        display: block;
        width: 22px;
        height: 2px;
        background: #fff;
        border-radius: 2px;
        transition: all var(--ease);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .header-search {
            max-width: 260px;
        }
    }

    @media (max-width: 768px) {
        .header-nav .container {
            flex-wrap: wrap;
        }

        .header-search {
            order: 1;
            max-width: none;
            flex: 1 1 100%;
        }

        .hamburger {
            display: flex;
            order: 2;
        }

        .nav-list {
            display: none;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            order: 3;
            padding: 8px 0 0;
        }

        .nav-list.open {
            display: flex;
        }

        .nav-list>li {
            width: 100%;
        }

        .nav-list>li>a {
            padding: 10px 4px;
        }

        .mega-menu {
            display: none;
        }
    }
</style>

<script>
    document.getElementById('hamburger')?.addEventListener('click', function () {
        document.querySelector('.nav-list')?.classList.toggle('open');
    });
</script>