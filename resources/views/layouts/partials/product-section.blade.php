{{-- @if(isset($products) && $products->count() > 0)
<section class="py-8">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="mb-6">
            <h2 class="text-[22px] font-bold text-[#2B3D5D]">{{ $title }}</h2>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                @include('layouts.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif --}}