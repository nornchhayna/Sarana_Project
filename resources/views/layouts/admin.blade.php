<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ashahi Admin</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    @stack('styles')


    <style>
        .sidebar-brand {
            font-weight: 700;
            font-size: 1.25rem;
        }
        .sidebar-brand span {
            color: #f0603f;
        }

        /* Prevent oversized pagination arrows and SVGs */
        .pagination svg,
        nav svg,
        div[role="navigation"] svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
        .pagination {
            margin-bottom: 0;
            display: inline-flex;
            flex-wrap: wrap;
        }
        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>

 <!-- SEARCH FORM -->
        <form class="form-inline ml-3">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.contacts.index') }}">
                    <i class="far fa-bell"></i>
                    @if(isset($unreadContacts) && $unreadContacts > 0)
                        <span class="badge badge-warning navbar-badge">{{ $unreadContacts }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/') }}" target="_blank" class="nav-link" title="View site">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </li>
            <!-- Logout -->
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link" style="border: none; background: none; padding: 0.5rem 1rem;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <div class="brand-text">
                Ashahi<span>.</span>
            </div>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            @php
                $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? auth()->user();
            @endphp
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('img/user-default.png') }}" class="img-circle elevation-2" alt="User Image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="img-circle elevation-2" style="width:34px; height:34px; min-width:34px; background:#f0603f; display:none; align-items:center; justify-content:center; color:white; font-weight:bold;">{{ strtoupper(substr($adminUser?->name ?? 'A', 0, 1)) }}</div>
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ $adminUser?->name ?? 'Administrator' }}</a>
                </div>
            </div>

            <!-- SidebarSearch Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Catalogue -->
                    <li class="nav-header">CATALOGUE</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-mobile-screen"></i>
                            <p>Products</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.gifts.index') }}" class="nav-link {{ request()->routeIs('admin.gifts.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gift"></i>
                            <p>Gifts</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Categories</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-blog"></i>
                            <p>Blogs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bookmark"></i>
                            <p>Brands</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.colors.index') }}" class="nav-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-palette"></i>
                            <p>Colors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.models.index') }}" class="nav-link {{ request()->routeIs('admin.models.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-microchip"></i>
                            <p>Models</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.storages.index') }}" class="nav-link {{ request()->routeIs('admin.storages.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Storage</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rams.index') }}" class="nav-link {{ request()->routeIs('admin.rams.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-memory"></i>
                            <p>RAM</p>
                        </a>
                    </li>

                    <!-- Inventory -->
                    <li class="nav-header">INVENTORY</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Stock</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.purchase-orders.index') }}" class="nav-link {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Purchase Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.stock-receivings.index') }}" class="nav-link {{ request()->routeIs('admin.stock-receivings.*') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-truck-ramp-box"></i>
                            <p>Stock Receivings</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.stock-movements.index') }}" class="nav-link {{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                            <p>Stock Movements</p>
                        </a>
                    </li>
                    <li class="nav-item">
                       <a href="{{ route('admin.suppliers.index') }}"
   class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-truck"></i>
    <p>Suppliers</p>
</a>
                    </li>

                    <!-- Sales -->
                    <li class="nav-header">SALES</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cart-shopping"></i>
                            <p>Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.invoices.index') }}" class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice"></i>
                            <p>Invoices</p>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="nav-header">REPORTS</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Reports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>All Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.sales-dashboard') }}" class="nav-link {{ request()->routeIs('admin.reports.sales-dashboard') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sales Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.inventory-valuation') }}" class="nav-link {{ request()->routeIs('admin.reports.inventory-valuation') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Inventory Valuation</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('admin.reports.product-performance') }}" class="nav-link {{ request()->routeIs('admin.reports.product-performance') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Product Performance</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.purchase-order-summary') }}" class="nav-link {{ request()->routeIs('admin.reports.purchase-order-summary') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Purchase Order Summary</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.financial-summary') }}" class="nav-link {{ request()->routeIs('admin.reports.financial-summary') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Financial Summary</p>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- Marketing -->
                    <li class="nav-header">MARKETING</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.promotions.index') }}" class="nav-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Promotions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.sliders.index') }}" class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-images"></i>
                            <p>Sliders</p>
                        </a>
                    </li>

                    <!-- Customers & Staff -->
                    <li class="nav-header">USERS & STAFF</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>All Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.webmasters.index') }}" class="nav-link {{ request()->routeIs('admin.webmasters.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Webmasters</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Reviews</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Contacts</p>
                        </a>
                    </li>

                    <!-- System -->
                    <li class="nav-header">SYSTEM</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.general') }}"
                   class="nav-link flex items-center gap-2 px-3 py-2 rounded-md text-gray-300 hover:bg-[var(--sidebar-hover)] hover:text-white {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    Settings
                </a>
               
             
                <a href="{{ route('admin.backups.index') }}"
                   class="nav-link flex items-center gap-2 px-3 py-2 rounded-md text-gray-300 hover:bg-[var(--sidebar-hover)] hover:text-white {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-database"></i>
                    Backups
                </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        {{ session('error') }}
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> Please fix the following:</h5>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="#">Ashahi</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
