@php
    $sold = $product->total_sold ?? 0;
    $available = $product->stock ? $product->stock->sum('stock') : 0;
    $total = $sold + $available;
    $percentage = $total > 0 ? ($sold / $total * 100) : 0;
@endphp

<div class="group relative bg-white border border-[#999aae] rounded-[8px] p-3 sm:p-4 md:p-5 flex flex-col h-full transition-all">

    {{-- Discount Badge --}}
    @if($product->sale_price && $product->base_price > $product->sale_price)
        @php
            $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100);
        @endphp
        <span class="absolute top-2 right-2 sm:top-4 sm:right-4 z-10 bg-[#1562c7] text-white text-[10px] sm:text-xs font-bold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full">
            -{{ $discount }}%
        </span>
    @endif

    {{-- Status Badge (Pre-order / New) --}}
    @if($product->is_pre_order)
        <span class="absolute top-2 left-2 sm:top-4 sm:left-4 z-10 bg-[#8E338A] text-white text-[10px] sm:text-xs font-bold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full">
            PRE-ORDER
        </span>
    @elseif($product->is_new)
        <span class="absolute top-2 left-2 sm:top-4 sm:left-4 z-10 bg-emerald-600 text-white text-[10px] sm:text-xs font-bold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full">
            NEW
        </span>
    @endif

    {{-- Product Image --}}
    <div class="w-full h-[110px] sm:h-[130px] md:h-[150px] flex items-center justify-center overflow-hidden mb-1">
        <a href="{{ route('products.show', $product->slug) }}" class="w-full h-full flex items-center justify-center">
            @if($product->thumbnail)
                <img src="{{ asset('storage/'.$product->thumbnail) }}" class="w-full h-full object-contain" loading="lazy" decoding="async">
            @else
                <div class="text-4xl sm:text-5xl md:text-6xl text-gray-300">📦</div>
            @endif
        </a>
    </div>

    {{-- Brand --}}
    <span class="text-xs text-[#adadad] -mb-1">
        {{ $product->brand->name ?? 'Brand' }}
    </span>

    {{-- Title --}}
    <a href="{{ route('products.show', $product->slug) }}"
       class="text-[13px] sm:text-sm font-bold text-[#044574] line-clamp-2 min-h-[28px] sm:min-h-[30px] leading-snug sm:leading-relaxed transition-colors duration-300 hover:text-[#1562c7]">
        {{ $product->name }}
    </a>

    {{-- Rating --}}
    <div class="flex items-center gap-1 -mt-1 mb-1 text-[11px] sm:text-xs text-[#f5b223]">
        <span class="text-[#adadad] mr-1">
            ({{ $product->reviews_count ?? 0 }})
        </span>
        @php $rating = round($product->avg_rating ?? 0); @endphp
        @for($i = 1; $i <= 5; $i++)
            <i class="fa-solid fa-star {{ $i <= $rating ? '' : 'text-gray-300' }}"></i>
        @endfor
    </div>

    {{-- Description --}}
    <div class="block mb-1">
        <p class="text-[12px] sm:text-[13px] text-[#7E7E7E] line-clamp-2 leading-tight min-h-[32px] sm:min-h-[34px]">
            {{ strip_tags($product->description) ?? 'No description available.' }}
        </p>
    </div>

    {{-- Price --}}
    <div class="mb-2 sm:mb-3 flex flex-wrap items-baseline gap-x-2">
        <span class="text-[15px] sm:text-[16px] md:text-[18px] font-bold text-[#0f4c81]">
            ${{ number_format($product->sale_price ?? $product->base_price, 2) }}
        </span>
        @if($product->sale_price)
            <span class="text-xs sm:text-sm text-[#adadad] line-through">
                ${{ number_format($product->base_price, 2) }}
            </span>
        @endif
    </div>

    {{-- Bottom Swap Area --}}
    <div class="relative min-h-[32px] mt-auto">

        {{-- Progress --}}
        <div class="absolute inset-0 flex flex-col justify-center transition-all duration-350 ease-out
            group-hover:opacity-0 group-hover:invisible group-hover:-translate-y-3">

            <div class="h-[6px] bg-blue-100 rounded-full overflow-visible">
                <div class="h-full bg-[#1562c7] rounded-full relative" style="width:{{ $percentage }}%">
                    <span class="absolute w-3 h-3 bg-[#1562c7] rounded-full right-[-6px] top-[-3px]"></span>
                </div>
            </div>

            <div class="flex justify-between text-[10px] sm:text-[11px] text-gray-500 mt-2">
                <span>Sold: <b class="text-[#253D4E]">{{ $sold }}</b></span>
                <span>Available: <b class="text-[#253D4E]">{{ $available }}</b></span>
            </div>
        </div>

        {{-- Cart / Pre-order Button --}}
        <div class="absolute inset-0 flex items-center opacity-0 invisible translate-y-3
            transition-all duration-400 ease-out
            group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">

            @if($product->is_pre_order)
                <a href="{{ route('products.show', $product->slug) }}"
                   class="w-full flex items-center justify-center gap-2 bg-purple-50 text-[#8E338A]
                   py-2 sm:py-2.5 rounded-md font-bold text-[13px] sm:text-sm transition-all duration-300
                   hover:bg-[#8E338A] hover:text-white hover:-translate-y-1">
                    <i class="fa-solid fa-box-open"></i> Pre-order Now
                </a>
            @else
                <a href="{{ route('products.show', $product->slug) }}"
                   class="w-full flex items-center justify-center gap-2 bg-blue-50 text-[#1562c7]
                   py-2 sm:py-2.5 rounded-md font-bold text-[13px] sm:text-sm transition-all duration-300
                   hover:bg-[#1562c7] hover:text-white hover:-translate-y-1">
                    <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                </a>
            @endif
        </div>
    </div>
</div>