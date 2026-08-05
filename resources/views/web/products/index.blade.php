@extends('layouts.app')

@section('title', 'Shop Products')
@section('page-title', 'Shop Products')

@section('content')

<div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-6 sm:py-8">

    {{-- Mobile Filter Trigger + Sort (visible only on small screens) --}}
    <div class="flex items-center justify-between gap-3 mb-4 lg:hidden">
        <button type="button" id="filter-toggle-btn"
                class="flex items-center gap-2 text-sm font-semibold text-[#2B3D5D] border border-[#e5e7eb] rounded-md px-4 py-2.5 bg-white">
            <i class="fa-solid fa-sliders"></i> Filters
        </button>
        <span class="text-xs text-[#4F5D77] whitespace-nowrap">
            {{ $products->total() }} found
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 lg:gap-8">

        {{-- ── OVERLAY (mobile only, closes drawer on click) ── --}}
<div id="filter-overlay" class="fixed inset-0 bg-black/40 z-[210] hidden lg:hidden"></div>

<aside id="filter-sidebar"
       class="bg-white border border-[#e5e7eb] rounded-[10px] p-5 h-fit
              fixed inset-y-0 left-0 z-[220] w-[85%] max-w-[320px] overflow-y-auto
              -translate-x-full transition-transform duration-300 ease-out
              lg:static lg:translate-x-0 lg:z-auto lg:w-auto lg:max-w-none">

            {{-- Mobile drawer header --}}
            <div class="flex items-center justify-between mb-4 lg:hidden">
                <h3 class="text-base font-bold text-[#2B3D5D]">Filters</h3>
                <button type="button" id="filter-close-btn" class="w-8 h-8 flex items-center justify-center text-[#4F5D77]">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('products.index') }}" method="GET" id="filter-form">

                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif

                {{-- Categories --}}
                <details class="mb-4 group" open>
                    <summary class="text-sm font-bold text-[#2B3D5D] mb-3 cursor-pointer list-none flex items-center justify-between">
                        Categories
                        <i class="fa-solid fa-chevron-down text-xs text-[#adadad] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <ul class="space-y-2 mt-3 max-h-[240px] overflow-y-auto pr-1">
                        <li>
                            <a href="{{ route('products.index') }}"
                               class="text-sm {{ !request('category') ? 'text-[#1562c7] font-bold' : 'text-[#4F5D77]' }} hover:text-[#1562c7]">
                                All Categories
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}"
                                   class="text-sm flex justify-between {{ request('category') === $category->slug ? 'text-[#1562c7] font-bold' : 'text-[#4F5D77]' }} hover:text-[#1562c7]">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-xs text-[#adadad]">({{ $category->products_count ?? 0 }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>

                {{-- Brands --}}
                <details class="mb-4 group border-t border-[#e5e7eb] pt-4" open>
                    <summary class="text-sm font-bold text-[#2B3D5D] mb-3 cursor-pointer list-none flex items-center justify-between">
                        Brand
                        <i class="fa-solid fa-chevron-down text-xs text-[#adadad] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <select name="brand" class="w-full text-sm border border-[#e5e7eb] rounded-md py-2 px-3 mt-3" onchange="document.getElementById('filter-form').submit()">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->slug }}" {{ request('brand') === $brand->slug ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </details>

                {{-- Price Range --}}
                <details class="mb-4 group border-t border-[#e5e7eb] pt-4" open>
                    <summary class="text-sm font-bold text-[#2B3D5D] mb-3 cursor-pointer list-none flex items-center justify-between">
                        Price Range
                        <i class="fa-solid fa-chevron-down text-xs text-[#adadad] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="flex items-center gap-2 mt-3">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                               class="w-1/2 text-sm border border-[#e5e7eb] rounded-md py-2 px-3">
                        <span class="text-[#adadad]">–</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                               class="w-1/2 text-sm border border-[#e5e7eb] rounded-md py-2 px-3">
                    </div>
                </details>

                {{-- Color --}}
                @if($colors->count())
                <details class="mb-4 group border-t border-[#e5e7eb] pt-4">
                    <summary class="text-sm font-bold text-[#2B3D5D] mb-3 cursor-pointer list-none flex items-center justify-between">
                        Color
                        <i class="fa-solid fa-chevron-down text-xs text-[#adadad] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($colors as $color)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="{{ $color->id }}"
                                       class="hidden peer" {{ (string) request('color') === (string) $color->id ? 'checked' : '' }}
                                       onchange="document.getElementById('filter-form').submit()">
                                <span class="block w-7 h-7 rounded-full border-2 peer-checked:border-[#2B3D5D]"
                                      style="background-color: {{ $color->hex_code ?? '#ccc' }};" title="{{ $color->name }}"></span>
                            </label>
                        @endforeach
                    </div>
                </details>
                @endif

                {{-- Storage --}}
                @if($storages->count())
                <details class="mb-4 group border-t border-[#e5e7eb] pt-4">
                    <summary class="text-sm font-bold text-[#2B3D5D] mb-3 cursor-pointer list-none flex items-center justify-between">
                        Storage
                        <i class="fa-solid fa-chevron-down text-xs text-[#adadad] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($storages as $storage)
                            <label class="cursor-pointer">
                                <input type="radio" name="storage" value="{{ $storage->id }}"
                                       class="hidden peer" {{ (string) request('storage') === (string) $storage->id ? 'checked' : '' }}
                                       onchange="document.getElementById('filter-form').submit()">
                                <span class="block text-xs font-semibold px-3 py-1.5 rounded-md border border-[#e5e7eb] peer-checked:bg-[#2B3D5D] peer-checked:text-white peer-checked:border-[#2B3D5D]">
                                    {{ $storage->label ?? $storage->capacity }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </details>
                @endif

                {{-- Feature Filters --}}
                <details class="mb-4 group border-t border-[#e5e7eb] pt-4" open>
                    <summary class="text-sm font-bold text-[#2B3D5D] mb-3 cursor-pointer list-none flex items-center justify-between">
                        Product Type
                        <i class="fa-solid fa-chevron-down text-xs text-[#adadad] group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="space-y-2 mt-3">
                        <label class="flex items-center gap-2 text-sm text-[#4F5D77] cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ request('is_featured') ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()">
                            Featured
                        </label>
                        <label class="flex items-center gap-2 text-sm text-[#4F5D77] cursor-pointer">
                            <input type="checkbox" name="is_new" value="1" {{ request('is_new') ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()">
                            New Arrival
                        </label>
                        <label class="flex items-center gap-2 text-sm text-[#4F5D77] cursor-pointer">
                            <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()">
                            On Sale
                        </label>
                    </div>
                </details>

                <a href="{{ route('products.index') }}" class="block text-center text-sm font-semibold text-[#1562c7] hover:underline mt-2">
                    Clear All Filters
                </a>

            </form>
        </aside>

        {{-- ── MAIN CONTENT ── --}}
        <div>

            {{-- Sort + Result count (desktop) --}}
            <div class="hidden lg:flex items-center justify-between mb-6">
                <span class="text-sm text-[#4F5D77]">
                    {{ $products->total() }} product{{ $products->total() === 1 ? '' : 's' }} found
                </span>

                <form action="{{ route('products.index') }}" method="GET">
                    @foreach(request()->except(['sort', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" class="text-sm border border-[#e5e7eb] rounded-md py-2 px-3" onchange="this.form.submit()">
                        <option value="default" {{ request('sort', 'default') === 'default' ? 'selected' : '' }}>Default</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="best_seller" {{ request('sort') === 'best_seller' ? 'selected' : '' }}>Best Sellers</option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Top Rated</option>
                    </select>
                </form>
            </div>

            {{-- Sort (mobile, full width) --}}
            <form action="{{ route('products.index') }}" method="GET" class="lg:hidden mb-4">
                @foreach(request()->except(['sort', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="sort" class="w-full text-sm border border-[#e5e7eb] rounded-md py-2.5 px-3" onchange="this.form.submit()">
                    <option value="default" {{ request('sort', 'default') === 'default' ? 'selected' : '' }}>Sort: Default</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="best_seller" {{ request('sort') === 'best_seller' ? 'selected' : '' }}>Best Sellers</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Top Rated</option>
                </select>
            </form>

            {{-- Product Grid --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                    @foreach($products as $product)
                        @include('layouts.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-16 sm:py-20 bg-white border border-[#e5e7eb] rounded-[10px] px-4">
                    <p class="text-[#4F5D77] mb-2">No products found matching your filters.</p>
                    <a href="{{ route('products.index') }}" class="text-[#1562c7] font-semibold hover:underline">Clear filters</a>
                </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('filter-sidebar');
    const overlay = document.getElementById('filter-overlay');
    const openBtn = document.getElementById('filter-toggle-btn');
    const closeBtn = document.getElementById('filter-close-btn');

    function openDrawer() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    openBtn?.addEventListener('click', openDrawer);
    closeBtn?.addEventListener('click', closeDrawer);
    overlay?.addEventListener('click', closeDrawer);
});
</script>
@endpush

@endsection