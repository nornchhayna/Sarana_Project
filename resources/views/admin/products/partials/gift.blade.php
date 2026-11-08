{{--
    resources/views/admin/products/partials/gifts.blade.php

    Include inside your product edit form, e.g.:
    @include('admin.products.partials.gifts', ['product' => $product, 'gifts' => $gifts])

    Requires: $product (Product, with ->gifts loaded), $gifts (all active Gift records)
--}}

<div class="mt-8 border border-gray-200 rounded-xl p-5 bg-white">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-base font-semibold text-[#253D4E]">Free Gifts Included</h3>
            <p class="text-sm text-gray-500 mt-0.5">Select the free items customers receive with this product.</p>
        </div>
        <a href="{{ route('admin.gifts.create') }}"
           target="_blank"
           class="text-sm font-medium text-[#f0603f] hover:text-[#d94e2f] transition">
            + New gift
        </a>
    </div>

    @if ($gifts->isEmpty())
        <p class="text-sm text-gray-400 italic">No gifts created yet. Create one first.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach ($gifts as $gift)
                @php $attached = $product->gifts->firstWhere('id', $gift->id); @endphp

                <label
                    for="gift-{{ $gift->id }}"
                    class="group flex items-center gap-3 border rounded-lg p-3 cursor-pointer transition
                           {{ $attached ? 'border-[#f0603f] bg-[#fff7f0]' : 'border-gray-200 hover:border-gray-300' }}"
                >
                    <input
                        type="checkbox"
                        name="gifts[]"
                        value="{{ $gift->id }}"
                        id="gift-{{ $gift->id }}"
                        {{ $attached ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-[#f0603f] focus:ring-[#f0603f]"
                        onchange="document.getElementById('qty-{{ $gift->id }}').disabled = !this.checked"
                    >

                    @if ($gift->image)
                        <img src="{{ asset('storage/' . $gift->image) }}"
                             alt="{{ $gift->name }}"
                             class="w-9 h-9 rounded-md object-cover border border-gray-100">
                    @else
                        <div class="w-9 h-9 rounded-md bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                            🎁
                        </div>
                    @endif

                    <span class="flex-1 text-sm text-[#253D4E] font-medium">{{ $gift->name }}</span>

                    <input
                        type="number"
                        name="gift_quantity[{{ $gift->id }}]"
                        id="qty-{{ $gift->id }}"
                        min="1"
                        value="{{ $attached->pivot->quantity ?? 1 }}"
                        {{ $attached ? '' : 'disabled' }}
                        class="w-14 text-sm border border-gray-200 rounded-md px-2 py-1 text-center
                               disabled:bg-gray-50 disabled:text-gray-300 focus:outline-none focus:ring-1 focus:ring-[#f0603f]"
                    >
                </label>
            @endforeach
        </div>
    @endif
</div>