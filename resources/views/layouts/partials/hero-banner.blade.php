{{-- layouts/partials/hero-banner.blade.php --}}

@php
    $banners = [
        ['image' => asset('storage/banners/image.png'), 'link' => '#'],
        ['image' => asset('storage/banners/17.png'), 'link' => '#'],
        ['image' => asset('storage/banners/12.png'), 'link' => '#'],
        ['image' => asset('storage/banners/122.png'), 'link' => '#'],
    ];
@endphp

@if(count($banners))
<section class="max-w-[1280px] mx-auto px-4 sm:px-6 py-4 sm:py-6">

    <div class="relative overflow-hidden rounded-[9px] shadow-[0_8px_30px_rgba(43,61,93,0.12)]">

        <div id="hero-track" class="flex transition-transform duration-700 ease-out">
            @foreach($banners as $banner)
                <a href="{{ $banner['link'] }}" class="relative shrink-0 w-full group">
                    <img src="{{ $banner['image'] }}" alt="Banner"
                         class="w-full h-[200px] sm:h-[320px] lg:h-[420px] object-cover">
                    {{-- subtle bottom gradient for depth / dot contrast --}}
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/25 to-transparent pointer-events-none"></div>
                </a>
            @endforeach
        </div>

        {{-- Prev / Next arrows --}}
        <button type="button" id="hero-prev" aria-label="Previous banner"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full
                   bg-white/90 backdrop-blur-sm shadow-md flex items-center justify-center
                   text-[#2B3D5D] hover:bg-white hover:scale-105 active:scale-95
                   transition-all duration-200">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </button>
        <button type="button" id="hero-next" aria-label="Next banner"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full
                   bg-white/90 backdrop-blur-sm shadow-md flex items-center justify-center
                   text-[#2B3D5D] hover:bg-white hover:scale-105 active:scale-95
                   transition-all duration-200">
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </button>

        {{-- Dots --}}
        <div id="hero-dots" class="absolute bottom-4 sm:bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2">
            @foreach($banners as $i => $banner)
                <button type="button"
                        class="hero-dot h-2 rounded-full bg-white/60 hover:bg-white/90 transition-all duration-300 {{ $i === 0 ? 'active' : '' }}"
                        data-index="{{ $i }}"
                        aria-label="Go to banner {{ $i + 1 }}"></button>
            @endforeach
        </div>

    </div>
</section>

<style>
    .hero-dot { width: 8px; }
    .hero-dot.active {
        width: 22px;
        background-color: #ffffff;
    }
</style>

<script>
(function () {
    const track = document.getElementById('hero-track');
    const dots = document.querySelectorAll('.hero-dot');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');
    const total = {{ count($banners) }};
    let current = 0;
    let timer;

    function goTo(index) {
        current = (index + total) % total;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((dot, i) => dot.classList.toggle('active', i === current));
    }

    function startAuto() {
        clearInterval(timer);
        timer = setInterval(() => goTo(current + 1), 5000);
    }

    prevBtn.addEventListener('click', () => { goTo(current - 1); startAuto(); });
    nextBtn.addEventListener('click', () => { goTo(current + 1); startAuto(); });
    dots.forEach(dot => dot.addEventListener('click', () => { goTo(+dot.dataset.index); startAuto(); }));

    goTo(0);
    startAuto();
})();
</script>
@endif