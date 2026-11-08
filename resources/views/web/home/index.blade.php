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
                    <img src="{{ asset('storage/76.jpg') }}" alt="" class="hero-bg-fixed">
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
                            @if($slider->button_text)
                                <div class="hero__actions">
                                    <a href="{{ $slider->button_url ?? '#' }}" class="btn-theme-dark">
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

        <div class="hero-dots" id="heroDots">
            @foreach($sliders as $slider)
                <button type="button" class="hero-dot {{ $loop->first ? 'hero-dot-active' : '' }}"data-index="{{ $loop->index }}"></button>
            @endforeach
        </div>
    @endif
</div>

@endif

{{-- Flash Sale Section --}}
@if($flashSaleProducts->count())
@include('layouts.partials.product-carousel', [
    'products' => $flashSaleProducts,
    'title' => 'Flash Sale',
    'sectionId' => 'flash-sale',
    'viewAllRoute' => route('products.index', ['is_flash_sale' => 1]),
    'showCountdown' => true,
])
@endif

@php
    $marqueeBrands = \App\Models\Brand::take(15)->get();
@endphp

<div class="brand-row-wrapper">
    <div class="container-fluid">
        <div class="brand-marquee">
            <div class="brand-track" id="brandTrack">
                <div class="brand-group">
                    <a href="{{ route('products.index') }}" class="brand-item active" title="All Brands">
                        <span>ALL</span>
                    </a>
                    @foreach($marqueeBrands as $brand)
                        <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="brand-item" title="{{ $brand->name }}">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}"
                                     alt="{{ $brand->name }} logo"
                                     width="120" height="80"
                                     loading="eager"
                                     decoding="async">
                            @else
                                <span>{{ $brand->name }}</span>
                            @endif
                        </a>
                    @endforeach
                    <a href="{{ route('products.index', ['category' => 'accessories']) }}" class="brand-item" title="Accessories">
                        <span>AUDIO</span>
                    </a>
                </div>

                <div class="brand-group" aria-hidden="true">
                    <a href="{{ route('products.index') }}" class="brand-item active" title="All Brands" tabindex="-1">
                        <span>ALL</span>
                    </a>
                    @foreach($marqueeBrands as $brand)
                        <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="brand-item" title="{{ $brand->name }}" tabindex="-1">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}"
                                     alt=""
                                     width="120" height="80"
                                     loading="eager"
                                     decoding="async">
                            @else
                                <span>{{ $brand->name }}</span>
                            @endif
                        </a>
                    @endforeach
                    <a href="{{ route('products.index', ['category' => 'accessories']) }}" class="brand-item" title="Accessories" tabindex="-1">
                        <span>AUDIO</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

@if($featuredProducts->count())
@include('layouts.partials.product-carousel', [
    'products' => $featuredProducts,
    'title' => __('messages.feature_product'),
    'sectionId' => 'featured',
    'viewAllRoute' => route('products.index', ['is_featured' => 1]),
])
@endif
{{-- ===============================
    Featured Categories
================================ --}}
@if($accessoryProducts->count())
@include('layouts.partials.product-carousel', [
    'title' => 'Accessories',
    'products' => $accessoryProducts,
    'sectionId' => 'accessories',
   'viewAllRoute' => route('categories.show', ['slug' => $accessoriesCategory->slug ?? 'accessories']),
])
@endif
<section class="py-3">

    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 ">
        {{-- Header --}}
        <div class="flex items-end justify-between mb-7">
            <div>
                <h2 class="text-[20px] font-bold text-[#253D4E] mb-1">
                    Featured Categories
                </h2>
            </div>
            {{-- Arrows --}}
            <div class="flex gap-3">
                <button id="cat-prev"
                    class="w-10 h-10 rounded-full bg-white border border-[#ececec] text-[#7E7E7E] flex items-center justify-center transition hover:bg-[#1562c7] hover:text-white hover:border-[#1562c7]">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <button id="cat-next"
                    class="w-10 h-10 rounded-full bg-white border border-[#ececec] text-[#7E7E7E] flex items-center justify-center transition hover:bg-[#1562c7] hover:text-white hover:border-[#1562c7]">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        {{-- Category Slider --}}
        <div id="category-slider"
             class="flex gap-5 overflow-x-auto scroll-smooth pb-4 scrollbar-hide">

            {{-- First copy --}}
            @foreach($categories as $category)
            <a href="{{ route('products.index',['category'=>$category->id]) }}"
               class="cat-item flex-none w-[120px] bg-white border border-[#ececec] rounded-[10px] px-3 py-5 text-center transition hover:-translate-y-1 hover:shadow-lg hover:border-[#bce3c9]">
                <div class="h-[60px] flex items-center justify-center mb-4">
                    <img
                        src="{{ asset('storage/'.$category->image) }}"
                        class="max-h-full max-w-full object-contain">
                </div>
                <h3 class="text-sm font-bold text-[#253D4E] leading-tight">
                    {{ $category->name }}
                </h3>
                <p class="text-xs text-[#adadad] mt-1">
                    {{ $category->products_count ?? 0 }} items
                </p>
            </a>
            @endforeach

            {{-- Duplicate copy (enables seamless infinite loop) --}}
            @foreach($categories as $category)
            <a href="{{ route('products.index',['category'=>$category->id]) }}"
               aria-hidden="true" tabindex="-1"
               class="cat-item flex-none w-[120px] bg-white border border-[#ececec] rounded-[10px] px-3 py-5 text-center transition hover:-translate-y-1 hover:shadow-lg hover:border-[#bce3c9]">
                <div class="h-[60px] flex items-center justify-center mb-4">
                    <img
                        src="{{ asset('storage/'.$category->image) }}"
                        class="max-h-full max-w-full object-contain">
                </div>
                <h3 class="text-sm font-bold text-[#253D4E] leading-tight">
                    {{ $category->name }}
                </h3>
                <p class="text-xs text-[#adadad] mt-1">
                    {{ $category->products_count ?? 0 }} items
                </p>
            </a>
            @endforeach
        </div>

    </div>
</section>

@if($newProducts->count())
@include('layouts.partials.product-carousel', [
    'products' => $newProducts,
    'title' => 'New Arrivals',
    'sectionId' => 'newarrivals',
    'viewAllRoute' => route('products.index', ['is_new' => 1]),
])
@endif

@include('layouts.partials.hero-banner')

@if($bestSellers->count())
@include('layouts.partials.product-carousel', [
    'title' => 'Best Sellers',
    'products' => $bestSellers,
    'sectionId' => 'best-sellers',
    'viewAllRoute' => route('products.index', ['sort' => 'best_seller']),
])
@endif
@if($preOrderProducts->count())
@include('layouts.partials.product-carousel', [
    'products' => $preOrderProducts,
    'title' => 'Pre-Order',
    'sectionId' => 'preorder',
    'viewAllRoute' => route('products.index', ['is_pre_order' => 1]),
])
@endif


{{-- Reviews Section --}}
<section class="mt-10 py-1">

    <div class="max-w-[1280px] mx-auto px-4">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <h2 class="text-2xl font-bold text-gray-800">
                What Our Customers Say
            </h2>

            @if($reviewCount > 0)

                <div class="flex items-center gap-2">

                    <span class="text-2xl font-bold text-gray-900">
                        {{ number_format($averageRating, 1) }}
                    </span>


                    <span class="text-yellow-400 text-lg">

                        @for ($i = 1; $i <= 5; $i++)

                            {{ $i <= round($averageRating) ? '★' : '☆' }}

                        @endfor

                    </span>


                    <span class="text-sm text-gray-500">
                        ({{ $reviewCount }} reviews)
                    </span>


                </div>

            @endif


        </div>

        {{-- Reviews Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse($latestReviews as $review)
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    {{-- User --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                            {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="font-semibold text-gray-800">
                            {{ $review->user->name ?? 'Anonymous' }}
                        </span>
                    </div>
                    {{-- Stars --}}
                    <div class="text-yellow-400 mb-3">

                        @for ($i = 1; $i <= 5; $i++)

                            {{ $i <= $review->rating ? '★' : '☆' }}

                        @endfor

                    </div>





                    {{-- Title --}}
                    @if($review->title)

                        <p class="font-semibold text-sm mb-2">
                            {{ $review->title }}
                        </p>
                    @endif

                    {{-- Body --}}
                    @if($review->body)

                        <p class="text-sm text-gray-600 mb-4">

                            {{ Str::limit($review->body, 120) }}

                        </p>

                    @endif





                    {{-- Images --}}
                    @if($review->images && count($review->images))

                        <div class="flex gap-2 mb-4">

                            @foreach(array_slice($review->images, 0, 3) as $imagePath)

                                <img
                                    src="{{ Storage::url($imagePath) }}"
                                    onclick="openLightbox('{{ Storage::url($imagePath) }}')"
                                    class="w-16 h-16 object-cover rounded-lg cursor-pointer border border-gray-200"
                                >
                            @endforeach
                        </div>
                    @endif
                    {{-- Footer --}}
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <a
                            href="{{ route('products.show', $review->product->slug ?? '') }}"
                            class="text-blue-600 font-semibold hover:underline"
                        >
                            {{ Str::limit($review->product->name ?? 'Product', 25) }}
                        </a>
                        <span>
                            {{ $review->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-gray-500">
                    No reviews yet.
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- Blog Section --}}
@if($blogPosts->count())
<section class="mt-10 -pt-1 pb-8">
    <div class="max-w-[1280px] mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Latest Blog Posts</h2>
            <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline">View All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($blogPosts as $post)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $post->title }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 hover:underline">Read More</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif


<div class="image-lightbox" id="image-lightbox" onclick="if(event.target === this) closeLightbox()">
    <button type="button" class="image-lightbox-close" onclick="closeLightbox()">&times;</button>
    <img id="lightbox-img" src="" alt="Review image">
</div>


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
   // Review image lightbox
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('image-lightbox').classList.add('is-open');
}

function closeLightbox() {
    document.getElementById('image-lightbox').classList.remove('is-open');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox();
});

  document.addEventListener('DOMContentLoaded', function () {
            const track = document.getElementById('brandTrack');
            if (!track) return;

            const images = track.querySelectorAll('img');
            let loaded = 0;
            const total = images.length;

            function setDuration() {
                // One full group is exactly half the track's scrollWidth
                const groupWidth = track.scrollWidth / 2;
                const pixelsPerSecond = 60; // adjust for faster/slower scroll
                const duration = Math.max(groupWidth / pixelsPerSecond, 10); // min 10s
                track.style.setProperty('--marquee-duration', duration + 's');
            }

            if (total === 0) {
                setDuration();
                return;
            }

            images.forEach(img => {
                if (img.complete) {
                    loaded++;
                } else {
                    img.addEventListener('load', check);
                    img.addEventListener('error', check);
                }
            });

            function check() {
                loaded++;
                if (loaded >= total) setDuration();
            }

            // In case all were already cached/complete
            if (loaded >= total) setDuration();

            // Recalculate on resize (debounced)
            let resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(setDuration, 200);
            });
        });
        

          (function () {
        const track = document.getElementById('category-slider');
        const prevBtn = document.getElementById('cat-prev');
        const nextBtn = document.getElementById('cat-next');

        const STEP_INTERVAL = 10000; // advance one card every 10 seconds
        let loopWidth = 0;           // width of ONE full set of items (half of total, since duplicated)
        let cardStep = 0;            // width of a single card + gap
        let timer = null;

        function measure() {
            loopWidth = track.scrollWidth / 2;
            const items = track.querySelectorAll('.cat-item');
            if (items.length > 1) {
                cardStep = items[1].offsetLeft - items[0].offsetLeft;
            } else if (items.length === 1) {
                cardStep = items[0].offsetWidth;
            }
        }

        function stepForward() {
            track.scrollBy({ left: cardStep, behavior: 'smooth' });

            // once we've scrolled past one full set, snap back invisibly
            // (works because the list is duplicated, so position 0 and
            // position loopWidth look identical)
            setTimeout(() => {
                if (track.scrollLeft >= loopWidth) {
                    track.scrollLeft -= loopWidth;
                }
            }, 500); // matches the smooth-scroll transition time
        }

        function startTimer() {
            clearInterval(timer);
            timer = setInterval(stepForward, STEP_INTERVAL);
        }

        function stopTimer() {
            clearInterval(timer);
        }

        // Pause while user hovers or touches the slider, resume after they leave
        track.addEventListener('mouseenter', stopTimer);
        track.addEventListener('mouseleave', startTimer);
        track.addEventListener('touchstart', stopTimer, { passive: true });
        track.addEventListener('touchend', () => setTimeout(startTimer, 1500));

        // Manual arrow buttons: scroll one card, then reset the 10s timer
        prevBtn.addEventListener('click', () => {
            track.scrollBy({ left: -cardStep, behavior: 'smooth' });
            startTimer();
        });

        nextBtn.addEventListener('click', () => {
            stepForward();
            startTimer();
        });

        window.addEventListener('resize', measure);

        measure();
        startTimer();
    })();
    </script>
@endpush

@endsection