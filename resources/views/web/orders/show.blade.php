@extends('layouts.app')

@section('content')

<div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-6">

    @php
        // ── Reusable line-icon set (currentColor, sized by the wrapping span) ──
        $icons = [
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>',
            'check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M5 12.5l4.5 4.5L19 7"/></svg>',
            'box' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M3.5 7.5L12 3l8.5 4.5L12 12 3.5 7.5z"/><path d="M3.5 7.5V16l8.5 4.5V12"/><path d="M20.5 7.5V16L12 20.5"/></svg>',
            'truck' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><rect x="2.5" y="8" width="11" height="8" rx="1"/><path d="M13.5 11h4l3 3v2h-7z"/><circle cx="7" cy="18.5" r="1.6"/><circle cx="17" cy="18.5" r="1.6"/></svg>',
            'house' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M4 11l8-7 8 7"/><path d="M6 10v9h12v-9"/><path d="M10 19v-5h4v5"/></svg>',
            'cross' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M6 6l12 12M18 6L6 18"/></svg>',
            'clipboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><rect x="6" y="4" width="12" height="17" rx="1.5"/><rect x="9" y="2.5" width="6" height="3" rx="1"/><path d="M9 11l1.5 1.5L13.5 9.5"/><path d="M9 15h6"/></svg>',
            'card' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><path d="M6.5 14.5h4"/></svg>',
            'printer' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M6 9V3h12v6"/><rect x="4" y="9" width="16" height="8" rx="1.5"/><path d="M6 14h12v7H6z"/></svg>',
            'download' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M4 19h16"/></svg>',
            'star' => '<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M12 2.5l2.9 6.2 6.8.7-5.1 4.6 1.5 6.7L12 17.3l-6.1 3.4 1.5-6.7-5.1-4.6 6.8-.7z"/></svg>',
            'phone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M5 4h4l1.5 4.5-2 1.5a12 12 0 006 6l1.5-2L20.5 15.5V19.5a1.5 1.5 0 01-1.6 1.5A16 16 0 013.5 6.1 1.5 1.5 0 015 4z"/></svg>',
            'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><rect x="3" y="5.5" width="18" height="13" rx="1.5"/><path d="M3.5 6.5L12 13l8.5-6.5"/></svg>',
            'arrow-left' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M19 12H5"/><path d="M11 6l-6 6 6 6"/></svg>',
        ];

        $icon = fn ($name, $class = 'w-4 h-4') => '<span class="inline-block '.$class.' align-[-3px]">'.$icons[$name].'</span>';

        $statusIconKey = match($order->status) {
            'pending' => 'clock',
            'confirmed' => 'check',
            'processing' => 'box',
            'shipped' => 'truck',
            'delivered' => 'house',
            'cancelled' => 'cross',
            default => 'clock'
        };

        $stepIconKey = fn ($statusKey) => match($statusKey) {
            'confirmed' => 'clipboard',
            'processing' => 'box',
            'shipped' => 'truck',
            'delivered' => 'house',
            default => 'clock'
        };

        $statusClass = match($order->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };

        $stripClass = match($order->status) {
            'pending' => 'bg-yellow-50 border-yellow-400 text-yellow-800',
            'confirmed', 'processing' => 'bg-blue-50 border-blue-400 text-blue-800',
            'shipped' => 'bg-indigo-50 border-indigo-400 text-indigo-800',
            'delivered' => 'bg-green-50 border-green-400 text-green-800',
            'cancelled' => 'bg-red-50 border-red-400 text-red-800',
            default => 'bg-gray-50 border-gray-400 text-gray-800'
        };

        $stripMessage = match($order->status) {
            'pending' => 'Your order has been placed and is awaiting payment confirmation.',
            'confirmed' => 'Payment verified — your order is being processed.',
            'processing' => 'Your order is being prepared for shipment.',
            'shipped' => 'Your order is on its way. Track it with the provided carrier details.',
            'delivered' => 'Delivered. Thank you for shopping with us!',
            'cancelled' => 'This order has been cancelled.',
            default => 'Your order is being processed.'
        };
    @endphp

    {{-- ── HEADER ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Order tracking</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $order->order_number }}</h1>
            <p class="text-gray-600 mt-1 text-sm">Placed on {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('H:i') }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-flex items-center gap-1.5 whitespace-nowrap">
            {!! $icon($statusIconKey, 'w-3.5 h-3.5') !!} {{ ucfirst($order->status) }}
        </span>
    </div>

    {{-- ── PROGRESS STEPPER ── --}}
    <div class="bg-white rounded-lg shadow-md p-5 sm:p-8 mb-6">

        {{-- Desktop / tablet: horizontal --}}
        <div class="hidden sm:flex items-start">
            @foreach($steps as $statusKey => $step)
                @php
                    $isCompleted = $step['index'] <= $currentStep;
                    $isActive = $step['index'] === $currentStep;
                @endphp
                <div class="flex flex-col items-center flex-1 relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center z-10
                        {{ $isCompleted ? 'bg-green-500 text-white' : ($isActive ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-400') }}">
                        {!! $icon($isCompleted ? 'check' : $stepIconKey($statusKey), 'w-5 h-5') !!}
                    </div>
                    <p class="mt-2.5 text-xs sm:text-sm font-semibold text-center {{ $isCompleted ? 'text-gray-800' : 'text-gray-400' }}">
                        {{ $step['label'] }}
                    </p>
                    @if($isCompleted && in_array($statusKey, ['confirmed', 'shipped', 'delivered']))
                        <p class="text-xs text-gray-400 mt-0.5">
                            @switch($statusKey)
                                @case('confirmed') {{ $order->confirmed_at?->format('M d') ?? '—' }} @break
                                @case('shipped') {{ $order->shipped_at?->format('M d') ?? '—' }} @break
                                @case('delivered') {{ $order->delivered_at?->format('M d') ?? '—' }} @break
                            @endswitch
                        </p>
                    @endif
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mt-5 {{ $step['index'] < $currentStep ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                @endif
            @endforeach
        </div>

        {{-- Mobile: vertical --}}
        <div class="sm:hidden">
            @foreach($steps as $statusKey => $step)
                @php
                    $isCompleted = $step['index'] <= $currentStep;
                    $isActive = $step['index'] === $currentStep;
                @endphp
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $isCompleted ? 'bg-green-500 text-white' : ($isActive ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-400') }}">
                            {!! $icon($isCompleted ? 'check' : $stepIconKey($statusKey), 'w-4 h-4') !!}
                        </div>
                        @if(!$loop->last)
                            <div class="w-0.5 flex-1 min-h-[24px] {{ $step['index'] < $currentStep ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                    <div class="pb-5 pt-0.5">
                        <p class="text-sm font-semibold {{ $isCompleted ? 'text-gray-800' : 'text-gray-400' }}">{{ $step['label'] }}</p>
                        @if($isCompleted && in_array($statusKey, ['confirmed', 'shipped', 'delivered']))
                            <p class="text-xs text-gray-400 mt-0.5">
                                @switch($statusKey)
                                    @case('confirmed') {{ $order->confirmed_at?->format('M d') ?? '—' }} @break
                                    @case('shipped') {{ $order->shipped_at?->format('M d') ?? '—' }} @break
                                    @case('delivered') {{ $order->delivered_at?->format('M d') ?? '—' }} @break
                                @endswitch
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── STATUS MESSAGE ── --}}
    <div class="{{ $stripClass }} border-l-4 rounded p-4 mb-6 text-sm font-medium flex items-center gap-2.5">
        {!! $icon($statusIconKey, 'w-4 h-4 flex-shrink-0') !!}
        <span>{{ $stripMessage }}</span>
    </div>

    {{-- ── ITEMS: MOBILE CARDS ── --}}
    <div class="md:hidden space-y-4 mb-6">
        @forelse($order->items as $item)
            @php
                // Try to get variant image matching the selected color
                $variantImage = null;
                if ($item->color_id && $item->product->variantImages) {
                    $variantImage = $item->product->variantImages
                        ->where('color_id', $item->color_id)
                        ->where('is_primary', true)
                        ->first();
                    if (!$variantImage) {
                        $variantImage = $item->product->variantImages
                            ->where('color_id', $item->color_id)
                            ->first();
                    }
                }
                
                // Fallback to product thumbnail or first image
                $imgPath = $variantImage?->image ?? 
                           $item->product->thumbnail ?? 
                           ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
            @endphp
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex gap-3">
                    <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}"
                         alt="{{ $item->product->name ?? '' }}"
                         class="w-14 h-14 rounded-lg object-cover border flex-shrink-0"
                         onerror="this.src='{{ asset('images/default-product.png') }}';">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800">{{ $item->product?->name ?? $item->product_name ?? 'Unknown Product' }}</div>
                        @if($item->color_name || $item->storage_capacity || $item->phoneStorage?->capacity)
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ collect([$item->color_name, $item->storage_capacity, $item->phoneStorage?->capacity])->filter()->implode(' • ') }}
                            </div>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">Qty {{ $item->quantity }} × {{ config('app.currency_symbol', '$') }}{{ number_format($item->unit_price, 2) }}</div>
                    </div>
                    <div class="text-sm font-bold text-gray-800 flex-shrink-0">{{ config('app.currency_symbol', '$') }}{{ number_format($item->subtotal, 2) }}</div>
                </div>
                @if($order->status === 'delivered')
                    <div class="mt-3 pt-3 border-t">
                        @if($item->product && in_array($item->product->id, $reviewedProductIds))
                            <span class="text-xs text-green-700 font-semibold inline-flex items-center gap-1">{!! $icon('check', 'w-3 h-3') !!} Reviewed</span>
                        @elseif($item->product)
                            <a href="{{ route('reviews.create', ['product' => $item->product->id, 'order_id' => $order->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-800 border border-yellow-200 text-xs font-bold rounded">
                                {!! $icon('star', 'w-3 h-3') !!} Write a review
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-400 text-sm">No items in this order.</div>
        @endforelse
    </div>

    {{-- ── ITEMS: TABLET / DESKTOP TABLE ── --}}
    <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Item</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Details</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Qty</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total</th>
                        @if($order->status === 'delivered')
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Review</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                    @php
                        // Try to get variant image matching the selected color
                        $variantImage = null;
                        if ($item->color_id && $item->product->variantImages) {
                            $variantImage = $item->product->variantImages
                                ->where('color_id', $item->color_id)
                                ->where('is_primary', true)
                                ->first();
                            if (!$variantImage) {
                                $variantImage = $item->product->variantImages
                                    ->where('color_id', $item->color_id)
                                    ->first();
                            }
                        }
                        
                        // Fallback to product thumbnail or first image
                        $imgPath = $variantImage?->image ?? 
                                   $item->product->thumbnail ?? 
                                   ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
                    @endphp
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}"
                                 alt="{{ $item->product->name ?? '' }}"
                                 class="w-14 h-14 rounded-lg object-cover border"
                                 onerror="this.src='{{ asset('images/default-product.png') }}';">
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-800">{{ $item->product?->name ?? $item->product_name ?? 'Unknown Product' }}</div>
                            @if($item->color_name || $item->storage_capacity || $item->phoneStorage?->capacity)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ collect([$item->color_name, $item->storage_capacity, $item->phoneStorage?->capacity])->filter()->implode(' • ') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-800">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-700">{{ config('app.currency_symbol', '$') }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($item->subtotal, 2) }}</td>
                        @if($order->status === 'delivered')
                            <td class="px-6 py-4 text-center">
                                @if($item->product && in_array($item->product->id, $reviewedProductIds))
                                    <span class="text-xs text-green-700 font-semibold inline-flex items-center gap-1">{!! $icon('check', 'w-3 h-3') !!} Reviewed</span>
                                @elseif($item->product)
                                    <a href="{{ route('reviews.create', ['product' => $item->product->id, 'order_id' => $order->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 text-yellow-800 border border-yellow-200 text-xs font-bold rounded whitespace-nowrap">
                                        {!! $icon('star', 'w-3 h-3') !!} Write a review
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ $order->status === 'delivered' ? 6 : 5 }}" class="px-6 py-14 text-center text-gray-400 text-sm">No items in this order.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── TOTALS ── --}}
        <div class="bg-gray-50 px-6 py-6 border-t">
            <div class="flex flex-col justify-end max-w-sm ml-auto space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->shipping_fee > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Shipping</span>
                    <span class="font-semibold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Tax</span>
                    <span class="font-semibold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="flex justify-between pb-2.5 border-b">
                    <span class="text-gray-600">Discount</span>
                    <span class="font-semibold text-green-600">-{{ config('app.currency_symbol', '$') }}{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @else
                <div class="pb-2.5 border-b"></div>
                @endif
                <div class="flex justify-between items-center pt-1">
                    <span class="text-base font-bold text-gray-800">Total</span>
                    <span class="text-lg font-bold text-blue-600">{{ config('app.currency_symbol', '$') }}{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TOTALS (mobile, shown below cards since table+totals block is hidden) ── --}}
    <div class="md:hidden bg-white rounded-lg shadow-md p-4 mb-8">
        <div class="space-y-2.5 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->shipping_fee > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping</span>
                <span class="font-semibold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($order->shipping_fee, 2) }}</span>
            </div>
            @endif
            @if($order->tax_amount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Tax</span>
                <span class="font-semibold text-gray-800">{{ config('app.currency_symbol', '$') }}{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif
            @if($order->discount_amount > 0)
            <div class="flex justify-between pb-2.5 border-b">
                <span class="text-gray-600">Discount</span>
                <span class="font-semibold text-green-600">-{{ config('app.currency_symbol', '$') }}{{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @else
            <div class="pb-2.5 border-b"></div>
            @endif
            <div class="flex justify-between items-center pt-1">
                <span class="text-base font-bold text-gray-800">Total</span>
                <span class="text-lg font-bold text-blue-600">{{ config('app.currency_symbol', '$') }}{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ── SHIPPING + ORDER INFO ── --}}
    @if($order->shipping_address)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500 mb-4">Shipping address</h3>
            <div class="text-sm text-gray-800 space-y-1.5">
                <p class="font-semibold">{{ $order->shipping_name }}</p>
                <p class="text-gray-600">{{ $order->shipping_address }}</p>
                @if($order->shipping_city)<p class="text-gray-600">{{ $order->shipping_city }}{{ $order->shipping_province ? ', ' . $order->shipping_province : '' }}</p>@endif
                @if($order->shipping_postal_code)<p class="text-gray-600">{{ $order->shipping_postal_code }}</p>@endif
                @if($order->shipping_country)<p class="text-gray-600">{{ $order->shipping_country }}</p>@endif
                <div class="pt-3 mt-3 border-t space-y-2">
                    <p class="text-gray-600 flex items-center gap-2">{!! $icon('phone', 'w-3.5 h-3.5 text-gray-400') !!} {{ $order->shipping_phone }}</p>
                    <p class="text-gray-600 flex items-center gap-2">{!! $icon('mail', 'w-3.5 h-3.5 text-gray-400') !!} {{ $order->shipping_email }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500 mb-4">Order information</h3>
            <div class="text-sm space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order number</span>
                    <span class="font-semibold text-gray-800">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order date</span>
                    <span class="font-semibold text-gray-800">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-semibold text-gray-800 capitalize">{{ $order->status }}</span>
                </div>
                @if($order->notes)
                <div class="pt-3 border-t">
                    <p class="text-gray-600 mb-1.5">Notes</p>
                    <p class="text-gray-800">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── ACTIONS ── --}}
    <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5">
            {!! $icon('arrow-left', 'w-3.5 h-3.5') !!} Back to orders
        </a>

        <div class="flex flex-wrap gap-2.5">
            @if($order->invoice)
                <a href="{{ route('orders.invoice.download', $order) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    {!! $icon('download', 'w-3.5 h-3.5') !!} Download invoice
                </a>
            @endif

            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition text-sm font-semibold">
                {!! $icon('printer', 'w-3.5 h-3.5') !!} Print
            </button>

            @if(in_array($order->status, ['pending', 'confirmed']))
                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm font-semibold">
                        {!! $icon('cross', 'w-3.5 h-3.5') !!} Cancel order
                    </button>
                </form>
            @endif

            @if($order->status === 'pending')
                <a href="{{ route('orders.khqr.pay', $order) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm font-semibold">
                    {!! $icon('card', 'w-3.5 h-3.5') !!} Pay now
                </a>
            @endif
        </div>
    </div>
</div>

@endsection