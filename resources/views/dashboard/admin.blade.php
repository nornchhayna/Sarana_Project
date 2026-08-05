<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ashahi Admin</title>

    {{-- Tailwind (utility classes) — loaded first so admin-pages.css below can still
         override shared element defaults like headings, inputs, tables --}}
 

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-pages.css') }}">
    @stack('styles')

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #253D4E;
            --navy-light: #33506A;
            --orange: #f0603f;
            --blue: #3f6fee;
            --bg: #F5F6F8;
            --card: #FFFFFF;
            --border: #E4E7ED;
            --input-bg: #F0F2F5;
            --text-1: #253D4E;
            --text-2: #6B7280;
            --text-3: #9CA3AF;
            --radius: 8px;
            --sidebar-w: 248px;
            --font-display: 'Sora', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text-1);
            font-size: 14px;
            line-height: 1.5;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--navy);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.2s ease;
        }

        .sidebar-logo {
            padding: 22px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .signal-mark {
            display: flex;
            align-items: flex-end;
            gap: 2px;
            height: 16px;
        }
        .signal-mark span {
            width: 4px;
            background: var(--orange);
            border-radius: 1px;
        }
        .signal-mark span:nth-child(1) { height: 5px; }
        .signal-mark span:nth-child(2) { height: 9px; }
        .signal-mark span:nth-child(3) { height: 13px; }
        .signal-mark span:nth-child(4) { height: 17px; background: rgba(255,255,255,0.25); }

        .logo-name {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.2px;
        }
        .logo-name span { color: var(--orange); }

        .nav-scroll { flex: 1; overflow-y: auto; padding-bottom: 12px; }

        .nav-section { padding: 16px 10px 4px; }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            padding: 0 12px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            color: rgba(255,255,255,0.7);
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-item i { width: 16px; text-align: center; font-size: 13px; }

        .nav-item:hover { background: rgba(255,255,255,0.06); color: #fff; }

        .nav-item.active {
            background: rgba(240, 96, 63, 0.14);
            color: #fff;
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--orange);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius);
        }

        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--orange);
            display: grid;
            place-items: center;
            font-family: var(--font-display);
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; overflow: hidden; }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 11px; color: rgba(255,255,255,0.4); }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            background: none;
            border: none;
            color: rgba(255,255,255,0.55);
            font-size: 13.5px;
            font-weight: 500;
            margin-top: 4px;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.06); color: #fff; }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 18px;
            color: var(--text-2);
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--input-bg);
            border-radius: var(--radius);
            padding: 8px 12px;
            color: var(--text-3);
        }
        .search-box input {
            background: none;
            border: none;
            outline: none;
            color: var(--text-1);
            font-family: var(--font-body);
            font-size: 13px;
            width: 180px;
        }
        .search-box input::placeholder { color: var(--text-3); }

        .icon-btn {
            width: 36px; height: 36px;
            background: var(--input-bg);
            border-radius: var(--radius);
            display: grid;
            place-items: center;
            color: var(--text-2);
            font-size: 14px;
            position: relative;
            border: none;
        }
        .icon-btn:hover { color: var(--navy); }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--orange);
            border: 2px solid var(--card);
        }

        .content { padding: 24px; flex: 1; }

        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: var(--radius);
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .alert-success { background: #EAF7EF; border: 1px solid #CBEBD8; color: #1F7A4D; }
        .alert-error   { background: #FDEDEB; border: 1px solid #F8CFC8; color: var(--orange); }
        .alert button { margin-left: auto; background: none; border: none; color: inherit; font-size: 13px; }
        .alert ul { list-style: disc; margin-left: 18px; }

        .footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--text-3);
            display: flex;
            justify-content: space-between;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .menu-toggle { display: block; }
            .search-box input { width: 120px; }
        }
    </style>
        <script src="https://cdn.tailwindcss.com"></script>
      @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="signal-mark"><span></span><span></span><span></span><span></span></div>
            <div class="logo-name">Ashahi<span>.</span></div>
        </div>

        <div class="nav-scroll">
            <nav class="nav-section">
                <div class="nav-label">Dashboard</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Catalogue</div>
                <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-mobile-screen"></i> Products
                </a>
                <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Categories
                </a>
                <a href="{{ route('admin.brands.index') }}" class="nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bookmark"></i> Brands
                </a>
                <a href="{{ route('admin.colors.index') }}" class="nav-item {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-palette"></i> Colors
                </a>
                <a href="{{ route('admin.models.index') }}" class="nav-item {{ request()->routeIs('admin.models.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-microchip"></i> Models
                </a>
                <a href="{{ route('admin.storages.index') }}" class="nav-item {{ request()->routeIs('admin.storages.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-database"></i> Storage
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Inventory</div>
                <a href="{{ route('admin.stock.index') }}" class="nav-item {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-warehouse"></i> Stock
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Sales</div>
                <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i> Orders
                </a>
                <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-credit-card"></i> Payments
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="nav-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i> Invoices
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Marketing</div>
                <a href="{{ route('admin.promotions.index') }}" class="nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> Promotions
                </a>
                <a href="{{ route('admin.sliders.index') }}" class="nav-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i> Sliders
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Customers</div>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Users
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i> Reviews
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="nav-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-envelope"></i> Contacts
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">System</div>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="page-title">@yield('title', 'Dashboard')</div>

            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass" style="font-size:12px;"></i>
                    <input type="text" placeholder="Search products, orders…">
                </div>
                <a href="{{ route('admin.contacts.index') }}" class="icon-btn">
                    <i class="fa-regular fa-bell"></i>
                    @if(isset($unreadContacts) && $unreadContacts > 0)
                        <span class="notif-dot"></span>
                    @endif
                </a>
                <a href="{{ url('/') }}" target="_blank" class="icon-btn" title="View site">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:13px;"></i>
                </a>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" style="align-items:flex-start; flex-direction:column;">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="footer">
            <span>© {{ date('Y') }} Ashahi. All rights reserved.</span>
            <span>Admin Panel v1.0</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>