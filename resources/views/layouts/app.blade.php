<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'PhoneHub – Premium phones & accessories')">

    <title>@yield('title', 'PhoneHub') — Premium Phones & Accessories</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700|space-grotesk:500,600,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ecom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wish.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pay.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    
    
    {{-- Page-specific CSS --}}
    @stack('styles')

   <style>
    /* ============================================================
       DESIGN TOKENS & BASE STYLES (Restored so your layout works!)
       ============================================================ */
    :root {
        --clr-primary: #3BB77E;
        --clr-primary-dark: #29A56C;
        --clr-primary-light: #DEF9EC;
        --clr-accent: #FCB800;
        --clr-danger: #F74B81;
        --clr-info: #4096FF;
        --clr-dark: #2B3D5D;
        --clr-body: #7E7E7E;
        --clr-muted: #B6B6B6;
        --clr-border: #EAEAEA;
        --clr-bg: #F2F3F4;
        --clr-white: #FFFFFF;
        --clr-surface: #F8F9FA;
        --font-display: 'Space Grotesk', sans-serif;
        --font-body: 'Inter', sans-serif;
        --sp-xs: 4px; --sp-sm: 8px; --sp-md: 16px; --sp-lg: 24px; --sp-xl: 40px; --sp-2xl: 64px;
        --container: 1200px;
        --radius-sm: 4px; --radius-md: 6px; --radius-lg: 10px; --radius-pill: 50px;
        --shadow-sm: 0 1px 4px rgba(0, 0, 0, .06);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08);
        --shadow-lg: 0 10px 25px rgba(0, 0, 0, .08);
        --ease: .25s ease-in-out;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: var(--font-body); font-size: 14px; color: var(--clr-body); background: var(--clr-white); line-height: 1.6; }
    h1, h2, h3, h4, h5, h6 { font-family: var(--font-display); color: var(--clr-dark); line-height: 1.25; }
    a { text-decoration: none; color: inherit; transition: color var(--ease); }
    img { max-width: 100%; display: block; }
    ul { list-style: none; }
    button { cursor: pointer; font-family: var(--font-body); border: none; background: none; }
    
    /* Crucial Container Class */
    .container { max-width: var(--container); margin: 0 auto; padding: 0 var(--sp-md); }

    /* Utility: Buttons */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 22px; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; font-family: var(--font-body); transition: all var(--ease); cursor: pointer; border: 1.5px solid transparent; white-space: nowrap; }
    .btn-primary { background: var(--clr-primary); color: var(--clr-white); border-color: var(--clr-primary); }
    .btn-primary:hover { background: var(--clr-primary-dark); border-color: var(--clr-primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59, 183, 126, .3); }
    
    /* Sections & Grid */
    .section { padding: var(--sp-2xl) 0; }
    .section-title { font-size: 22px; font-weight: 700; color: var(--clr-dark); }
</style>
</head>

<body>
@auth
    @include('components.welcome-modal')
@endauth
    @include('layouts.partials.flash-message')
    @hasSection('no_topbar')
    @else
        <div class="topbar-announcement">
            <div class="container">
  <span>  ហាងលក់ទូរស័ព្ទដៃធំជាងគេលើពិភពលោក&nbsp;|&nbsp; ប្រើប្រាស់ Promote code <strong>គុកព្រៃស</strong> ដើម្បីទទួលបានការបញ្ចុះតំលៃ 10%</span>            </div>
        </div>
    @endif
    @include('layouts.partials.navbar')

    <main id="main-content">
        @yield('content')
    </main>

    @include('layouts.partials.footer')


    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')

    <style>
        .topbar-announcement { background: var(--clr-dark); color: #fff; font-size: 12.5px; padding: 8px 0; }
        .topbar-announcement .container { display: flex; align-items: center; justify-content: space-between; }
        @media (max-width: 600px) { .topbar-announcement .container { justify-content: center; } }
    </style>
</body>
</html>