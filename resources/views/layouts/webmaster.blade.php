<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ashahi Webmaster</title>

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
                <a href="{{ route('webmaster.dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3" action="{{ route('webmaster.products.index') }}" method="GET">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" name="search" value="{{ request('search') }}" placeholder="Search products…" aria-label="Search">
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
                <a class="nav-link" href="{{ route('webmaster.contacts.index') }}" title="Customer Messages">
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
        <a href="{{ route('webmaster.dashboard') }}" class="brand-link">
            <div class="brand-text sidebar-brand">
                Ashahi<span>.</span> <span class="badge badge-primary ml-1" style="font-size: 10px; letter-spacing: 1px; vertical-align: middle;">WEBMASTER</span>
            </div>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            @php
                $wmUser = \Illuminate\Support\Facades\Auth::guard('webmaster')->user() ?? auth()->user();
            @endphp
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <div class="img-circle elevation-2 d-flex align-items-center justify-content-center" style="width:34px; height:34px; min-width:34px; background:#f0603f; color:white; font-weight:bold;">
                        {{ strtoupper(substr($wmUser?->name ?? 'W', 0, 1)) }}
                    </div>
                </div>
                <div class="info">
                    <a href="{{ route('webmaster.dashboard') }}" class="d-block">{{ $wmUser?->name ?? 'Webmaster' }}</a>
                    <small class="text-muted" style="font-size: 11px;">Webmaster</small>
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
                        <a href="{{ route('webmaster.dashboard') }}" class="nav-link {{ request()->routeIs('webmaster.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Catalogue -->
                    <li class="nav-header">CATALOGUE</li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.products.index') }}" class="nav-link {{ request()->routeIs('webmaster.products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-mobile-screen"></i>
                            <p>Products</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.categories.index') }}" class="nav-link {{ request()->routeIs('webmaster.categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Categories</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.brands.index') }}" class="nav-link {{ request()->routeIs('webmaster.brands.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bookmark"></i>
                            <p>Brands</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.colors.index') }}" class="nav-link {{ request()->routeIs('webmaster.colors.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-palette"></i>
                            <p>Colors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.models.index') }}" class="nav-link {{ request()->routeIs('webmaster.models.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-microchip"></i>
                            <p>Models</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.storages.index') }}" class="nav-link {{ request()->routeIs('webmaster.storages.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Storage</p>
                        </a>
                    </li>

                    <!-- Inventory -->
                    <li class="nav-header">INVENTORY</li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.stock.index') }}" class="nav-link {{ request()->routeIs('webmaster.stock.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Stock</p>
                            @if(isset($lowStockBadge) && $lowStockBadge > 0)
                                <span class="badge badge-danger right">{{ $lowStockBadge }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Sales -->
                    <li class="nav-header">SALES</li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.orders.index') }}" class="nav-link {{ request()->routeIs('webmaster.orders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cart-shopping"></i>
                            <p>Orders</p>
                            @if(isset($pendingOrdersBadge) && $pendingOrdersBadge > 0)
                                <span class="badge badge-warning right">{{ $pendingOrdersBadge }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.payments.index') }}" class="nav-link {{ request()->routeIs('webmaster.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.invoices.index') }}" class="nav-link {{ request()->routeIs('webmaster.invoices.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice"></i>
                            <p>Invoices</p>
                        </a>
                    </li>

                    <!-- Marketing -->
                    <li class="nav-header">MARKETING</li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.promotions.index') }}" class="nav-link {{ request()->routeIs('webmaster.promotions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Promotions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.sliders.index') }}" class="nav-link {{ request()->routeIs('webmaster.sliders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-images"></i>
                            <p>Sliders</p>
                        </a>
                    </li>

                    <!-- Customers -->
                    <li class="nav-header">CUSTOMERS</li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.reviews.index') }}" class="nav-link {{ request()->routeIs('webmaster.reviews.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Reviews</p>
                            @if(isset($pendingReviewsCount) && $pendingReviewsCount > 0)
                                <span class="badge badge-info right">{{ $pendingReviewsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('webmaster.contacts.index') }}" class="nav-link {{ request()->routeIs('webmaster.contacts.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Contacts</p>
                            @if(isset($unreadContacts) && $unreadContacts > 0)
                                <span class="badge badge-danger right">{{ $unreadContacts }}</span>
                            @endif
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
                            <li class="breadcrumb-item"><a href="{{ route('webmaster.dashboard') }}">Home</a></li>
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
            <b>Webmaster Panel</b> v1.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark"></aside>
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
