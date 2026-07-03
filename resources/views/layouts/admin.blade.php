<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - PhoneHub</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0C0E14;
            --surface: #13161F;
            --card: #1A1E2B;
            --border: #252A3A;
            --accent: #5B6EF5;
            --accent-2: #00D4AA;
            --accent-3: #FF5C7A;
            --accent-4: #F5A623;
            --text-1: #E8EAF2;
            --text-2: #8B90A7;
            --text-3: #555B72;
            --mono: 'JetBrains Mono', monospace;
            --sans: 'Space Grotesk', sans-serif;
            --sidebar-w: 260px;
            --radius: 12px;
        }

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text-1);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .sidebar-logo {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: var(--surface);
            z-index: 2;
        }

        .logo-mark {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 18px;
        }

        .logo-name {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--text-1);
        }

        .logo-name span { color: var(--accent); }

        .sidebar-nav-wrapper {
            flex: 1;
            padding-bottom: 20px;
        }

        .nav-section {
            padding: 24px 16px 8px;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-3);
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--text-2);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 4px;
        }

        .nav-item:hover {
            background: var(--card);
            color: var(--text-1);
        }

        .nav-item.active {
            background: rgba(91, 110, 245, 0.15);
            color: var(--accent);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        /* ── SIDEBAR FOOTER ── */
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            position: sticky;
            bottom: 0;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: var(--radius);
            background: var(--card);
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid;
            place-items: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; overflow: hidden; }
        .user-name { font-size: 14px; font-weight: 600; color: var(--text-1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 12px; color: var(--text-3); }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: rgba(255, 92, 122, 0.1);
            color: var(--accent-3);
            border: 1px solid transparent;
            border-radius: 8px;
            font-family: var(--sans);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: var(--accent-3);
            color: #fff;
        }

        /* ── MAIN CONTENT ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: rgba(19, 22, 31, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-1);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-3);
            margin-top: 4px;
        }

        .breadcrumb span.current { color: var(--text-2); }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 16px;
            color: var(--text-2);
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .search-box:focus-within { border-color: var(--accent); }
        .search-box input { background: none; border: none; outline: none; color: var(--text-1); font-family: var(--sans); width: 220px; }
        .search-box input::placeholder { color: var(--text-3); }

        .icon-btn {
            width: 40px;
            height: 40px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: grid;
            place-items: center;
            cursor: pointer;
            color: var(--text-2);
            font-size: 16px;
            transition: all 0.2s;
            position: relative;
            text-decoration: none;
        }

        .icon-btn:hover { color: var(--text-1); border-color: var(--accent); }

        .notif-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-3);
            border: 2px solid var(--surface);
        }

        /* ── ALERTS (Flash Messages) ── */
        .alert-container { padding: 32px 32px 0 32px; }
        .alert { display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: var(--radius); margin-bottom: 16px; font-size: 14px; font-weight: 500; }
        .alert i { font-size: 18px; }
        .alert-success { background: rgba(0, 212, 170, 0.1); border: 1px solid var(--accent-2); color: var(--accent-2); }
        .alert-error { background: rgba(255, 92, 122, 0.1); border: 1px solid var(--accent-3); color: var(--accent-3); }
        .alert-close { margin-left: auto; cursor: pointer; background: none; border: none; color: inherit; font-size: 16px; opacity: 0.7; transition: opacity 0.2s; }
        .alert-close:hover { opacity: 1; }
        
        .alert-list { margin-top: 8px; margin-left: 28px; list-style-type: disc; font-size: 13px; font-weight: 400; color: var(--accent-3); }

        /* ── PAGE CONTENT ── */
        .content {
            padding: 32px;
            flex: 1;
        }

        /* ── MAIN FOOTER ── */
        .main-footer {
            padding: 24px 32px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-3);
            font-size: 13px;
        }
    </style>
</head>

<body>

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">

        {{-- LOGO --}}
        <div class="sidebar-logo">
            <div class="logo-mark">
                <!-- <div class="logo-icon"></div> -->
                <div class="logo-name"> Ashahi<span> Phone Store</span></div>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <div class="sidebar-nav-wrapper">
            
            {{-- DASHBOARD --}}
            <nav class="nav-section">
                <div class="nav-label">Dashboard</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </nav>

            {{-- CATALOGUE --}}
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
                    <i class="fa-solid fa-mobile"></i> Models
                </a>
                <a href="{{ route('admin.storages.index') }}" class="nav-item {{ request()->routeIs('admin.storages.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-database"></i> Storage
                </a>
                 <a href="{{ route('admin.sliders.index') }}" class="nav-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i> Sliders
                </a>
            </nav>

            {{-- INVENTORY & SALES --}}
            <nav class="nav-section">
               
                <div class="nav-label">Operations</div>
                <a href="{{ route('admin.stock.index') }}" class="nav-item {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-warehouse"></i> Stock
                </a>
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

            {{-- MARKETING & CUSTOMERS --}}
            <nav class="nav-section">
                <div class="nav-label">Growth</div>
                <a href="{{ route('admin.promotions.index') }}" class="nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> Promotions
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Users
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i> Reviews
                </a>
            </nav>
        </div>

        {{-- SIDEBAR FOOTER (User & Logout) --}}
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name ?? 'Admin User' }}</div>
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

    {{-- ── MAIN CONTENT AREA ── --}}
    <div class="main">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div>
                <div class="page-title">@yield('title', 'Dashboard')</div>
                <div class="breadcrumb">
                    <i class="fa-solid fa-house" style="font-size:11px;"></i>
                    <span>/</span>
                    <span class="current">@yield('title', 'Overview')</span>
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search products, orders…">
                </div>

                {{-- Notifications --}}
                <a href="{{ route('admin.contacts.index') }}" class="icon-btn" title="Messages">
                    <i class="fa-regular fa-envelope"></i>
                    @if(isset($unreadContacts) && $unreadContacts > 0)
                        <span class="notif-dot"></span>
                    @endif
                </a>

                {{-- View Site --}}
                <a href="{{ url('/') }}" target="_blank" class="icon-btn" title="View Live Site">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i>
                </a>

                {{-- Refresh --}}
                <button class="icon-btn" id="refreshBtn" title="Refresh Data">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <div class="alert-container">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-regular fa-circle-check"></i>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="alert-close"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="alert-close"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" style="flex-direction: column; align-items: flex-start;">
                    <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <strong>Please fix the following errors:</strong>
                        <button onclick="this.parentElement.parentElement.remove()" class="alert-close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <ul class="alert-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- INJECTED PAGE CONTENT --}}
        <main class="content">
            @yield('content')
        </main>

        {{-- MAIN FOOTER --}}
        <footer class="main-footer">
            <span>© {{ date('Y') }} PhoneHub. All rights reserved.</span>
            <span>Admin Panel v1.0</span>
        </footer>

    </div>

    {{-- SCRIPTS --}}
    <script>
        // Refresh button animation & functionality
        document.getElementById('refreshBtn').addEventListener('click', function () {
            const icon = this.querySelector('i');
            icon.style.transform = 'rotate(360deg)';
            icon.style.transition = 'transform 0.5s ease';
            setTimeout(() => { 
                icon.style.transform = ''; 
                // Uncomment line below to enable actual page refresh
                // window.location.reload();
            }, 500);
        });
    </script>
</body>
</html>