<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PhoneHub Admin — Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,
        *::before,
        *::after {
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
            --sidebar-w: 240px;
            --radius: 12px;
        }

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text-1);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
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
        }

        .sidebar-logo {
            padding: 28px 24px 24px;
            border-bottom: 1px solid var(--border);
        }

        .logo-mark {
            display: flex;
            align-items: center;
            gap: 10px;
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
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--text-1);
        }

        .logo-name span {
            color: var(--accent);
        }

        .nav-section {
            padding: 20px 12px 8px;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-3);
            padding: 0 12px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--text-2);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
            cursor: pointer;
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
            width: 18px;
            text-align: center;
            font-size: 14px;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent-3);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            font-family: var(--mono);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            background: var(--card);
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            overflow: hidden;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
        }

        .user-role {
            font-size: 11px;
            color: var(--text-3);
        }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-3);
        }

        .breadcrumb span {
            color: var(--text-2);
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
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 12px;
            color: var(--text-2);
            font-size: 13px;
            font-family: var(--sans);
            transition: border-color 0.15s;
        }

        .search-box input {
            background: none;
            border: none;
            outline: none;
            color: var(--text-1);
            font-family: var(--sans);
            font-size: 13px;
            width: 180px;
        }

        .search-box input::placeholder {
            color: var(--text-3);
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: grid;
            place-items: center;
            cursor: pointer;
            color: var(--text-2);
            font-size: 14px;
            transition: all 0.15s;
            position: relative;
        }

        .icon-btn:hover {
            color: var(--text-1);
            border-color: var(--accent);
        }

        .notif-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent-3);
            border: 2px solid var(--surface);
        }

        /* ── CONTENT ── */
        .content {
            padding: 32px;
            flex: 1;
        }

        /* ── STATS GRID ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.c1::before {
            background: var(--accent);
        }

        .stat-card.c2::before {
            background: var(--accent-2);
        }

        .stat-card.c3::before {
            background: var(--accent-3);
        }

        .stat-card.c4::before {
            background: var(--accent-4);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-3);
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
            font-family: var(--mono);
            color: var(--text-1);
            margin-bottom: 8px;
        }

        .stat-delta {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            font-family: var(--mono);
        }

        .delta-up {
            color: var(--accent-2);
        }

        .delta-down {
            color: var(--accent-3);
        }

        .stat-icon {
            position: absolute;
            bottom: 16px;
            right: 18px;
            font-size: 32px;
            opacity: 0.07;
        }

        /* ── MIDDLE ROW ── */
        .mid-row {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 16px;
            margin-bottom: 28px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-1);
        }

        .card-sub {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .btn-ghost {
            font-size: 12px;
            color: var(--accent);
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--sans);
            font-weight: 500;
        }

        /* ── CHART AREA ── */
        .chart-area {
            padding: 24px 22px;
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 140px;
            margin-bottom: 10px;
        }

        .bar-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            height: 100%;
            justify-content: flex-end;
        }

        .bar {
            width: 100%;
            border-radius: 4px 4px 0 0;
            min-height: 4px;
            transition: opacity 0.15s;
        }

        .bar:hover {
            opacity: 0.75;
        }

        .bar.revenue {
            background: var(--accent);
        }

        .bar.orders {
            background: rgba(91, 110, 245, 0.3);
        }

        .bar-label {
            font-size: 10px;
            color: var(--text-3);
            font-family: var(--mono);
        }

        .chart-legend {
            display: flex;
            gap: 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--text-2);
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
        }

        /* ── RECENT ORDERS ── */
        .orders-list {
            padding: 8px 0;
        }

        .order-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 22px;
            transition: background 0.1s;
        }

        .order-row:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .order-thumb {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--border);
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .order-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
        }

        .order-id {
            font-size: 11px;
            color: var(--text-3);
            font-family: var(--mono);
        }

        .order-price {
            margin-left: auto;
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
        }

        .status-badge {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 20px;
            font-family: var(--mono);
        }

        .status-paid {
            background: rgba(0, 212, 170, 0.12);
            color: var(--accent-2);
        }

        .status-pending {
            background: rgba(245, 166, 35, 0.12);
            color: var(--accent-4);
        }

        .status-shipped {
            background: rgba(91, 110, 245, 0.12);
            color: var(--accent);
        }

        .status-cancelled {
            background: rgba(255, 92, 122, 0.12);
            color: var(--accent-3);
        }

        /* ── BOTTOM ROW ── */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ── TABLE ── */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-3);
            padding: 10px 22px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            border-bottom: 1px solid rgba(37, 42, 58, 0.5);
            transition: background 0.1s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        tbody td {
            padding: 11px 22px;
            font-size: 13px;
            color: var(--text-2);
        }

        .prod-name {
            font-weight: 600;
            color: var(--text-1);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prod-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--border);
            display: grid;
            place-items: center;
            font-size: 14px;
        }

        .stock-bar-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stock-bar {
            flex: 1;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }

        .stock-fill {
            height: 100%;
            border-radius: 2px;
        }

        .fill-good {
            background: var(--accent-2);
        }

        .fill-mid {
            background: var(--accent-4);
        }

        .fill-low {
            background: var(--accent-3);
        }

        .stock-num {
            font-family: var(--mono);
            font-size: 12px;
            min-width: 30px;
            text-align: right;
        }

        /* ── ACTIVITY FEED ── */
        .activity-list {
            padding: 8px 22px 16px;
        }

        .activity-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(37, 42, 58, 0.4);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .act-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .act-text {
            font-size: 12.5px;
            color: var(--text-2);
            line-height: 1.5;
        }

        .act-text strong {
            color: var(--text-1);
            font-weight: 600;
        }

        .act-time {
            margin-left: auto;
            font-size: 10.5px;
            color: var(--text-3);
            font-family: var(--mono);
            white-space: nowrap;
        }
    </style>
</head>

<body>

 {{-- ── SIDEBAR ── --}}
<aside class="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="logo-mark">
            <div class="logo-icon">📱</div>
            <div class="logo-name">
                Phone<span>Hub</span>
            </div>
        </div>
    </div>

    {{-- DASHBOARD --}}
    <nav class="nav-section">
        <div class="nav-label">Dashboard</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            Dashboard
        </a>
    </nav>

    {{-- CATALOGUE --}}
    <nav class="nav-section">
        <div class="nav-label">Catalogue</div>

        <a href="{{ route('admin.products.index') }}"
           class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="fa-solid fa-mobile-screen"></i>
            Products
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i>
            Categories
        </a>

        <a href="{{ route('admin.brands.index') }}"
           class="nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="fa-solid fa-bookmark"></i>
            Brands
        </a>

        <a href="{{ route('admin.colors.index') }}"
           class="nav-item {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
            <i class="fa-solid fa-palette"></i>
            Colors
        </a>

        <a href="{{ route('admin.models.index') }}"
           class="nav-item {{ request()->routeIs('admin.models.*') ? 'active' : '' }}">
            <i class="fa-solid fa-mobile"></i>
            Models
        </a>

        <a href="{{ route('admin.storages.index') }}"
           class="nav-item {{ request()->routeIs('admin.storages.*') ? 'active' : '' }}">
            <i class="fa-solid fa-database"></i>
            Storage
        </a>
    </nav>

    {{-- INVENTORY --}}
    
<nav class="nav-section">
        <div class="nav-label">Inventory</div>

        <a href="{{ route('admin.stock.index') }}"
           class="nav-item {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
            <i class="fa-solid fa-warehouse"></i>
            Stock
        </a>
    </nav>
    {{-- SALES --}}
    <nav class="nav-section">
        <div class="nav-label">Sales</div>

        <a href="{{ route('admin.orders.index') }}"
           class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping"></i>
            Orders
        </a>

        <a href="{{ route('admin.payments.index') }}"
           class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i>
            Payments
        </a>

        <a href="{{ route('admin.invoices.index') }}"
           class="nav-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice"></i>
            Invoices
        </a>
    </nav>

    {{-- MARKETING --}}
    <nav class="nav-section">
        <div class="nav-label">Marketing</div>

        <a href="{{ route('admin.promotions.index') }}"
           class="nav-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags"></i>
            Promotions
        </a>

        <a href="{{ route('admin.sliders.index') }}"
           class="nav-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
            <i class="fa-solid fa-images"></i>
            Sliders
        </a>
    </nav>

    {{-- CUSTOMERS --}}
    <nav class="nav-section">
        <div class="nav-label">Customers</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            Users
        </a>

        <a href="{{ route('admin.reviews.index') }}"
           class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="fa-solid fa-star"></i>
            Reviews
        </a>

        <a href="{{ route('admin.contacts.index') }}"
           class="nav-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
            <i class="fa-solid fa-envelope"></i>
            Contacts
        </a>
    </nav>

    {{-- SETTINGS --}}
    <nav class="nav-section">
        <div class="nav-label">System</div>

        <a href="#" class="nav-item">
            <i class="fa-solid fa-gear"></i>
            Settings
        </a>
    </nav>
    <nav class="nav-section">
    <div class="nav-label">Inventory</div>

    <a href="{{ route('admin.stock.index') }}"
       class="nav-item {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
        <i class="fa-solid fa-warehouse"></i>
        Stock
    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-item logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </button>
    </form>
</nav>
<div class="lg:ml-64 min-h-screen flex flex-col">

    {{-- Top bar --}}
    <header class="sticky top-0 z-20 bg-white border-b border-gray-200 h-14 flex items-center px-4 gap-4">
        {{-- Mobile menu button --}}
        <button onclick="toggleSidebar()"
                class="lg:hidden text-gray-500 hover:text-gray-700">
            <i class="ti ti-menu-2 text-xl"></i>
        </button>

        {{-- Page title --}}
        <h1 class="text-base font-semibold text-gray-800 flex-1">
            @yield('title', 'Dashboard')
        </h1>

        {{-- Right actions --}}
        <div class="flex items-center gap-3">

            {{-- Notification bell --}}
            <a href="{{ route('admin.contacts.index') }}"
               class="relative text-gray-500 hover:text-gray-700">
                <i class="ti ti-bell text-xl"></i>
                @if(isset($unreadContacts) && $unreadContacts > 0)
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </a>

            {{-- View site --}}
            <a href="{{ url('/') }}" target="_blank"
               class="text-gray-500 hover:text-gray-700" title="View site">
                <i class="ti ti-external-link text-xl"></i>
            </a>
        </div>
    </header>

    {{-- Flash messages --}}
    <div class="px-6 pt-4 space-y-2">
        @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm" role="alert">
                <i class="ti ti-circle-check text-green-500 text-lg shrink-0"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i class="ti ti-x text-base"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm" role="alert">
                <i class="ti ti-alert-circle text-red-500 text-lg shrink-0"></i>
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                    <i class="ti ti-x text-base"></i>
                </button>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm" role="alert">
                <div class="flex items-center gap-2 font-medium mb-1">
                    <i class="ti ti-alert-circle text-red-500 text-lg"></i>
                    Please fix the following errors:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Page content --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="px-6 py-4 border-t border-gray-200 text-xs text-gray-400 flex items-center justify-between">
        <span>© {{ date('Y') }} Ashahi. All rights reserved.</span>
        <span>Admin Panel v1.0</span>
    </footer>

</div>
    {{-- FOOTER --}}
    <div class="sidebar-footer">

        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>

            <div class="user-info">
                <div class="user-name">
                    {{ auth()->user()->name }}
                </div>

                <div class="user-role">
                    Administrator
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </button>
        </form>

    </div>

</aside>
    {{-- ── MAIN ── --}}
    <div class="main">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div>
                <div class="page-title">Dashboard</div>
                <div class="breadcrumb">
                    <i class="fa-solid fa-house" style="font-size:10px;"></i>
                    <span>/</span>
                    <span>Overview</span>
                </div>
            </div>
            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass" style="font-size:12px;"></i>
                    <input type="text" placeholder="Search products, orders…">
                </div>
                <div class="icon-btn">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notif-dot"></span>
                </div>
                <div class="icon-btn">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
            </div>
        </header>


        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
        // Refresh stats button
        document.querySelector('.fa-arrows-rotate').closest('.icon-btn').addEventListener('click', function () {
            this.style.transform = 'rotate(360deg)';
            this.style.transition = 'transform 0.5s ease';
            setTimeout(() => { this.style.transform = ''; }, 500);
            // Trigger AJAX refresh or reload
            // window.location.reload();
        });
    </script>

</body>

</html>