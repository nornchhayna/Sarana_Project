{{-- resources/views/home/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Shop the latest smartphones and accessories at PhoneHub.')



@section('content')

@if($sliders->count())
<div id="heroCarousel" class="relative w-full overflow-hidden" data-interval="10000">

    <div id="heroCarouselInner" class="hero-carousel-inner">
        @foreach($sliders as $slider)
            <div class="hero-slide {{ $loop->first ? 'hero-slide-active' : '' }}" data-index="{{ $loop->index }}">
                <section class="hero">
                    {{-- FIXED BACKGROUND (same on every slide) --}}
                    <img src="{{ asset('storage/67.jpg') }}" alt="" class="hero-bg-fixed">

                    {{-- SLIDER'S OWN IMAGE, LAYERED ON TOP --}}
                    <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" class="hero-slide-img">

                    <div class="container relative z-10">
                        <div class="hero__text">
                            @if($slider->badge_text)
                                <div class="hero__eyebrow">
                                    {{ $slider->badge_text }}
                                </div>
                            @endif

                            <h1 class="hero__title">
                                {{ $slider->title }}
                            </h1>

                            @if($slider->subtitle)
                                <p class="hero__desc">
                                    {{ $slider->subtitle }}
                                </p>
                            @endif

                            @if($slider->button_text && $slider->button_url)
                                <div class="hero__actions">
                                    <a href="{{ $slider->button_url }}" class="btn-theme-dark">
                                        {{ $slider->button_text }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    @if($sliders->count() > 1)
        <button type="button" id="heroPrevBtn" class="hero-nav-btn hero-nav-prev">
            <span class="hero-nav-icon hero-nav-icon-prev"></span>
        </button>

        <button type="button" id="heroNextBtn" class="hero-nav-btn hero-nav-next">
            <span class="hero-nav-icon hero-nav-icon-next"></span>
        </button>

        {{-- Dots --}}
        <div class="hero-dots" id="heroDots">
            @foreach($sliders as $slider)
                <button type="button"
                    class="hero-dot {{ $loop->first ? 'hero-dot-active' : '' }}"
                    data-index="{{ $loop->index }}"></button>
            @endforeach
        </div>
    @endif
</div>


@endif

{{-- ── BRAND LOGO ROW (Animated Marquee) ── --}}
@php
    $marqueeBrands = \App\Models\Brand::take(8)->get();
@endphp

<div class="brand-row-wrapper">
    <div class="container-fluid"> 
        <div class="brand-marquee">
            <div class="brand-track">
                
                {{-- GROUP 1 --}}
                <div class="brand-group">
                    <a href="{{ route('products.index') }}" class="brand-item active" title="All Brands">
                        <span>ALL</span>
                    </a>
                    @foreach($marqueeBrands as $brand)
                        <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="brand-item" title="{{ $brand->name }}">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }} logo">
                            @else
                                <span>{{ $brand->name }}</span>
                            @endif
                        </a>
                    @endforeach
                    <a href="{{ route('products.index', ['category' => 'accessories']) }}" class="brand-item" title="Accessories">
                        <span>AUDIO</span>
                    </a>
                </div>

                {{-- GROUP 2 --}}
                <div class="brand-group" aria-hidden="true">
                    <a href="{{ route('products.index') }}" class="brand-item active" title="All Brands">
                        <span>ALL</span>
                    </a>
                    @foreach($marqueeBrands as $brand)
                        <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="brand-item" title="{{ $brand->name }}">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }} logo">
                            @else
                                <span>{{ $brand->name }}</span>
                            @endif
                        </a>
                    @endforeach
                    <a href="{{ route('products.index', ['category' => 'accessories']) }}" class="brand-item" title="Accessories">
                        <span>AUDIO</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

    <section class="ecom-cat-section">
    <div class="container">
        
        {{-- Header & Arrows --}}
        <div class="ecom-cat-header">
            <div class="ecom-cat-title-wrap">
                <h2>Featured Categories</h2>
                <p>Choose your necessary products from these feature categories.</p>
            </div>
            
            {{-- Navigation Arrows --}}
            <div class="ecom-cat-nav">
                <button class="ecom-cat-btn" id="cat-prev">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button class="ecom-cat-btn" id="cat-next">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        {{-- The Slider Area --}}
        <div class="ecom-cat-slider" id="category-slider">
            
            {{-- Dynamic Loop for your database categories --}}
            @foreach($categories as $category)
             <a href="{{ route('products.index', ['category' => $category->id]) }}" class="ecom-cat-card">
                    <div class="ecom-cat-img">
                        {{-- Make sure to use your actual category image column here --}}
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                    </div>
                    <span class="ecom-cat-name">{{ $category->name }}</span>
                    <span class="ecom-cat-items">{{ $category->products_count ?? 0 }} items</span>
                </a>
            @endforeach

           
        </div>

    </div>
</section>
<section class="section" style="padding: 20px 0;">
    {{-- CRITICAL: This container keeps the grid from stretching full width --}}
    <div class="container"> 
        
        {{-- Section Header --}}
        <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px;">
            <h2 style="font-size: 22px; font-weight: 700; color: #2B3D5D; margin: 0;">Deals of the Day</h2>
        </div>

        {{-- CRITICAL: This creates the 4-column layout --}}
        <div class="ecom-products-grid">
            @foreach($featuredProducts as $product)
                <div class="ecom-deal-card">
                    
                    {{-- Discount Badge --}}
                    @if($product->sale_price && $product->base_price > $product->sale_price)
                        @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                        <span class="ecom-deal-badge">-{{ $discount }}%</span>
                    @endif

                    {{-- Image --}}
                    <div class="ecom-deal-img">
                       <a href="{{ route('products.show', $product->slug) }}">
                            @if($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}">
                            @else
                                <div style="font-size:60px; color:#D1D5DB;">📦</div>
                            @endif
                        </a>
                    </div>

                   

                    {{-- Product Info --}}
                    <span class="ecom-deal-brand">{{ $product->brand->name ?? 'Brand' }}</span>
                    <a href="{{ route('products.show', $product->id) }}" class="ecom-deal-title">{{ $product->name }}</a>
                    
                    <div class="ecom-deal-rating">
                        <span>({{ $product->reviews_count ?? rand(10,50) }})</span>
                        @php $rating = round($product->avg_rating ?? rand(4,5)); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $rating ? '' : 'star-empty' }}"></i>
                        @endfor
                    </div>
<div class="ecom-deal-desc" title="{{ strip_tags($product->description) }}">
                        {{ strip_tags($product->description) ?? 'No description available for this product.' }}
                    </div>
                    <div class="ecom-deal-price">
                        <span class="ecom-deal-price-current">${{ number_format($product->sale_price ?? $product->base_price, 2) }}</span>
                        @if($product->sale_price && $product->base_price > $product->sale_price)
                            <span class="ecom-deal-price-old">${{ number_format($product->base_price, 2) }}</span>
                        @endif
                    </div>

                  {{-- THE SWAP CONTAINER --}}
                    <div class="ecom-deal-swap-area">
                        
                        {{-- 1. Progress Bar --}}
                        @php
                            $sold = rand(100, 300); 
                            $available = rand(50, 200); 
                            $total = $sold + $available;
                            $percentage = ($sold / $total) * 100;
                        @endphp
                        <div class="ecom-deal-progress-wrap">
                            <div class="ecom-deal-progress-bar">
                                <div class="ecom-deal-progress-fill" style="width: {{ $percentage }}%;">
                                    <div class="ecom-deal-progress-thumb"></div>
                                </div>
                            </div>
                            <div class="ecom-deal-stats">
                                <div>Sold: <span>{{ $sold }}</span></div>
                                <div>Available: <span>{{ $available }}</span></div>
                            </div>
                        </div>

                        {{-- 2. Add to Cart Button --}}
                      <div class="ecom-deal-action-wrapper">
    <a href="{{ route('products.show', $product->slug) }}" class="ecom-deal-btn-cart">
        <i class="fa-solid fa-cart-shopping" style="margin-right:8px;"></i>
        Add to Cart
    </a>
</div>

                    </div>
                   

                  

                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script>
       document.addEventListener("DOMContentLoaded", function() {
        const slider = document.getElementById('category-slider');
        const btnPrev = document.getElementById('cat-prev');
        const btnNext = document.getElementById('cat-next');

        // The amount to slide per click (Approx 2-3 cards)
        const scrollAmount = 350; 

        btnNext.addEventListener('click', () => {
            slider.scrollLeft += scrollAmount;
        });

        btnPrev.addEventListener('click', () => {
            slider.scrollLeft -= scrollAmount;
        });
    });
    (function () {
    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;

    const slides = Array.from(carousel.querySelectorAll('.hero-slide'));
    const dots = Array.from(carousel.querySelectorAll('.hero-dot'));
    const prevBtn = document.getElementById('heroPrevBtn');
    const nextBtn = document.getElementById('heroNextBtn');
    const interval = parseInt(carousel.dataset.interval || '10000', 10);

    let current = 0;
    let timer = null;

    function showSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;

        slides.forEach((slide, i) => {
            slide.classList.toggle('hero-slide-active', i === index);
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('hero-dot-active', i === index);
        });

        current = index;
    }

    function next() {
        showSlide(current + 1);
    }

    function prev() {
        showSlide(current - 1);
    }

    function startAutoplay() {
        stopAutoplay();
        timer = setInterval(next, interval);
    }

    function stopAutoplay() {
        if (timer) clearInterval(timer);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            next();
            startAutoplay(); // reset timer on manual interaction
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prev();
            startAutoplay();
        });
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            showSlide(parseInt(dot.dataset.index, 10));
            startAutoplay();
        });
    });

    if (slides.length > 1) {
        startAutoplay();
    }
})();
    </script>
@endpush