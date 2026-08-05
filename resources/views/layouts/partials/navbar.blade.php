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

    <div class="dropdown-menu">
        <a href="{{ route('lang.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
            🇬🇧 {{ __('messages.english') }}
        </a>
        <a href="{{ route('lang.switch', 'km') }}" class="dropdown-item {{ app()->getLocale() == 'km' ? 'active' : '' }}">
            🇰🇭 {{ __('messages.khmer') }}
        </a>
    </div>
</div>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist.index') }}" class="icon-link position-relative" title="Wishlist">
                    <i class="fa-regular fa-heart"></i>
                    @if($wishlistCount ?? 0)
                        <span class="icon-badge">{{ $wishlistCount }}</span>
                    @endif
                </a>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="icon-link position-relative" title="Cart">
                    <i class="fa-solid fa-bag-shopping"></i>
                    @if($cartCount ?? 0)
                        <span class="icon-badge ">{{ $cartCount }}</span>
                    @endif
                </a>
                <div class="flex items-center gap-4">
                    <x-notification-bell />
                    {{-- other nav items --}}
                </div>
                {{-- Account --}}
                @guest
                    <a href="{{ route('auth.login') }}" class="icon-link" title="Sign in">
                        <i class="fa-regular fa-user"></i>
                    </a>
                @else
                  <div class="icon-link dropdown-trigger" title="Account" id="account-dropdown-trigger">
                    <i class="fa-regular fa-user"></i>
                    <div class="dropdown-menu">
                        <a href="{{ route('profile.index') }}"><i class="fa-regular fa-user"></i> Profile</a>
                        <a href="{{ route('orders.index') }}"><i class="fa-solid fa-bag-shopping"></i> My Orders</a>
                        <a href="{{ route('wishlist.index') }}"><i class="fa-regular fa-heart"></i> Wishlist</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" data-logout-link>
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </div>
                  </div>
                  <form data-logout-form action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
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
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}"
                    class="search-input" autocomplete="off">
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
<ul class="nav-list">
    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('messages.home') }}</a></li>

  <li class="has-mega">
    <a href="{{ route('products.index') }}"
        class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
        {{ __('messages.phone') }}
    </a>
    <!-- <button type="button" class="mega-toggle" aria-label="Toggle Phone menu" aria-expanded="false">
        <i class="fa-solid fa-chevron-down text-xs"></i>
    </button> -->
    <div class="mega-menu">
        <div class="mega-col">
            <div class="mega-label">{{ __('messages.brands') }}</div>
            @foreach(\App\Models\Brand::take(8)->get() as $brand)
                <a href="{{ route('products.index', ['brand' => $brand->slug]) }}">{{ $brand->name }}</a>
            @endforeach
        </div>
        <div class="mega-col">
            <div class="mega-label">{{ __('messages.products') }}</div>
            @foreach(\App\Models\Category::take(8)->get() as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
            @endforeach
        </div>
        <div class="mega-col">
            <div class="mega-label">Shop By</div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}">New Arrivals</a>
            <a href="{{ route('products.index', ['sort' => 'best_seller']) }}">Best Sellers</a>
            <a href="{{ route('products.index', ['on_sale' => 1]) }}">On Sale</a>
        </div>
    </div>
</li>
    <li><a href="{{ route('products.index', ['category' => 'accessories']) }}">{{ __('messages.accessories') }}</a></li>
    <li><a href="{{ route('contact.index') }}">{{ __('messages.contact') }}</a></li>
    <li><a href="{{ route('orders.index') }}">{{ __('messages.my_orders') }}</a></li>
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
        top: -13px;
        right: -15px;
        min-width: 21px;
        height: 21px;
        background: #e30b0b;
        color: #f9f9f9;
        border-radius: 50%;
        font-size: 9px;
        font-weight: 700;
        display: grid;
        place-items: center;
        border: 2px solid var(--clr-white);
    }
    /* Account + language dropdown, and mega menu — must beat the hamburger */
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
    z-index: 600; /* was 300 */
}

.mega-menu {
    /* ...unchanged... */
    z-index: 600; /* was 300 */
}

/* Hamburger button itself — keep it clickable, but below dropdowns */
.hamburger {
    /* ...unchanged... */
    z-index: 550; /* was 500 */
}

/* Mobile nav-list — give it an explicit, lower z-index */
.nav-list {
    /* ...unchanged... */
    z-index: 400;
    position: relative; /* needed for z-index to apply */
}

    /* Account dropdown */
    .dropdown-trigger {
        position: relative;
    }

    .dropdown-trigger:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .nav-item-row {
    display: flex;
    align-items: center;
    gap: 6px;
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
/* Keep hover for desktop pointer devices */
@media (hover: hover) {
    .dropdown-trigger:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
}

/* Click/tap-driven state for touch devices (and desktop click too) */
.dropdown-trigger.open .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
    /* =========================
   Hamburger Menu
========================= */

.hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 5px;
    width: 38px;
    height: 38px;
    padding: 8px;
    background: transparent;
    border: 1px solid #10336b;
    border-radius: 8px;
    cursor: pointer;
    z-index: 500;
}

.hamburger span {
    width: 22px;
    height: 2px;
    background: #10336b;
    border-radius: 5px;
    transition: all .3s ease;
}


/* Hamburger animation */
.hamburger.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.hamburger.active span:nth-child(2) {
    opacity: 0;
}

.hamburger.active span:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
}


/* =========================
   Mobile Responsive
========================= */

@media (max-width:768px) {

    .header-nav .container {
        flex-wrap: wrap;
        position: relative;
    }


    /* Search */
    .header-search {
        order: 1;
        width: calc(100% - 55px);
        max-width: none;
    }


    /* Hamburger show */
    .hamburger {
        display: flex;
        order: 2;
        margin-left: auto;
    }


    /* Mobile menu */
    .nav-list {
        display: none;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        order: 3;
        margin-top: 10px;
        padding: 10px;
        background: #fff;
        border-radius: 12px;

        box-shadow: 0 8px 25px rgba(0,0,0,.15);
    }


    .nav-list.open {
        display: flex;
    }


    .nav-list > li {
        width:100%;
    }


    .nav-list > li > a {
        width:100%;
        padding:12px 10px;
        color:#10336b;
    }


    /* Hide mega menu on mobile */
    .mega-menu {
        display:none;
    }

}
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // Hamburger
    const hamburger = document.getElementById('hamburger');
    const navList = document.querySelector('.nav-list');
    hamburger?.addEventListener('click', function () {
        navList.classList.toggle('open');
        hamburger.classList.toggle('active');
    });

    // Mega menu (unchanged logic, kept as-is)
    document.querySelectorAll('.has-mega').forEach(function (item) {
        const toggleBtn = item.querySelector('.mega-toggle');
        toggleBtn?.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = item.classList.contains('mega-open');
            document.querySelectorAll('.has-mega.mega-open').forEach(function (openItem) {
                if (openItem !== item) {
                    openItem.classList.remove('mega-open');
                    openItem.querySelector('.mega-toggle')?.setAttribute('aria-expanded', 'false');
                }
            });
            item.classList.toggle('mega-open', !isOpen);
            toggleBtn.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    // ── Account dropdown + logout, via delegation ──
    document.addEventListener('click', function (e) {

        const trigger = e.target.closest('#account-dropdown-trigger');
        const logoutLink = e.target.closest('[data-logout-link]');
        const mega = e.target.closest('.has-mega');

        // Logout: handle first, before anything else can interfere
        if (logoutLink) {
            e.preventDefault();
            const form = document.querySelector('[data-logout-form]');
            form?.submit();
            return;
        }

        // Toggle account dropdown
        if (trigger) {
            trigger.classList.toggle('open');
        } else {
            document.querySelectorAll('.dropdown-trigger.open').forEach(t => t.classList.remove('open'));
        }

        // Close mega menu if clicking fully outside it
        if (!mega) {
            document.querySelectorAll('.has-mega.mega-open').forEach(function (openItem) {
                openItem.classList.remove('mega-open');
                openItem.querySelector('.mega-toggle')?.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // ── Theme toggle ──
    const toggleBtn = document.getElementById('theme-toggle');

    console.log('Theme toggle button found:', !!toggleBtn);

    if (toggleBtn) {
        // Load saved theme preference
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        console.log('Saved theme:', savedTheme, 'Prefers dark:', prefersDark);
        
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            document.documentElement.classList.add('dark');
            console.log('Added dark class on load');
        }

        toggleBtn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            console.log('Theme toggled to:', isDark ? 'dark' : 'light');
            console.log('HTML classes:', document.documentElement.classList.toString());
            console.log('HTML element has dark class:', document.documentElement.classList.contains('dark'));
        });
    }

    // Optional: live-update if user changes OS setting while tab is open
    // and they haven't manually overridden it yet
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            document.documentElement.classList.toggle('dark', e.matches);
        }
    });

});

document.querySelector('.dropdown-trigger').addEventListener('click', function (e) {
    this.classList.toggle('open');
});
document.addEventListener('click', function (e) {
    if (!e.target.closest('.dropdown-trigger')) {
        document.querySelector('.dropdown-trigger').classList.remove('open');
    }
});
</script>
