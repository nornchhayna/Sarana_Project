@if(isset($products) && $products->count() > 0)
<section class="py-6 sm:py-8">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-[17px] sm:text-[20px] font-bold text-[#2B3D5D]">{{ $title }}</h2>

            <div class="flex items-center gap-2 sm:gap-3">
                @if(isset($viewAllRoute))
                    <a href="{{ $viewAllRoute }}" class="text-[13px] sm:text-sm font-semibold text-[#1562c7] hover:underline">
                        View All
                    </a>
                @endif

                <button type="button" class="carousel-prev-{{ $sectionId }} w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-[#999aae] flex items-center justify-center text-[#2B3D5D] hover:bg-[#1562c7] hover:text-white hover:border-[#1562c7] transition-colors">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button type="button" class="carousel-next-{{ $sectionId }} w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-[#999aae] flex items-center justify-center text-[#2B3D5D] hover:bg-[#1562c7] hover:text-white hover:border-[#1562c7] transition-colors">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>


<div
    id="carousel-track-{{ $sectionId }}"
    class="carousel-track flex gap-2 sm:gap-3 md:gap-4 lg:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar"
>
    @foreach($products as $product)
        <div class="carousel-item snap-start shrink-0 w-[calc(50%-4px)] sm:w-[calc(33.333%-8px)] md:w-[calc(25%-12px)] lg:w-[calc(20%-19.2px)]">
            @include('layouts.partials.product-card', ['product' => $product])
        </div>
    @endforeach
</div>

    </div>
</section>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
(function () {
    const track = document.getElementById('carousel-track-{{ $sectionId }}');
    if (!track) return;

    const prevBtn = document.querySelector('.carousel-prev-{{ $sectionId }}');
    const nextBtn = document.querySelector('.carousel-next-{{ $sectionId }}');

    function cardWidth() {
        const card = track.querySelector(':scope > div');
        if (!card) return 0;
        const gap = window.innerWidth < 640 ? 8 : 24; // matches gap-2 / gap-6
        return card.getBoundingClientRect().width + gap;
    }

    function scrollByCards(count) {
        track.scrollBy({ left: cardWidth() * count, behavior: 'smooth' });
    }

    function atEnd() {
        return track.scrollLeft + track.clientWidth >= track.scrollWidth - 5;
    }

    prevBtn?.addEventListener('click', () => scrollByCards(-1));
    nextBtn?.addEventListener('click', () => scrollByCards(1));

    // Auto-advance every 15s, looping back to start at the end
    setInterval(() => {
        if (atEnd()) {
            track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            scrollByCards(1);
        }
    }, 15000);
})();
</script>
@endif