@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    .ot-page {
        --ink: #14181F;
        --slate: #5B6472;
        --slate-soft: #8A93A3;
        --line: #E4E7EC;
        --line-soft: #EEF0F3;
        --canvas: #F8F9FB;
        --surface: #FFFFFF;
        --brand: #2952E3;
        --brand-ink: #1E3FBF;
        --brand-tint: #EEF1FE;
        --success: #157A4E;
        --success-tint: #E9F7F0;
        --warning: #B5730A;
        --warning-tint: #FDF3E3;
        --info: #2166D1;
        --info-tint: #E9F1FD;
        --danger: #C4362E;
        --danger-tint: #FBEBEA;
        --radius: 14px;
        background: var(--canvas);
        color: var(--ink);
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .ot-page h1, .ot-page h2, .ot-page h3, .ot-page .ot-display {
        font-family: 'Sora', 'Inter', sans-serif;
    }

    .ot-mono {
        font-family: 'IBM Plex Mono', ui-monospace, monospace;
        letter-spacing: 0.02em;
    }

    .ot-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
    }

    .ot-shadow {
        box-shadow: 0 1px 2px rgba(20, 24, 31, 0.04), 0 8px 24px -12px rgba(20, 24, 31, 0.08);
    }

    /* Status badge */
    .ot-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .ot-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .ot-badge--pending { background: var(--warning-tint); color: var(--warning); }
    .ot-badge--confirmed { background: var(--info-tint); color: var(--info); }
    .ot-badge--processing { background: var(--info-tint); color: var(--info); }
    .ot-badge--shipped { background: var(--brand-tint); color: var(--brand-ink); }
    .ot-badge--delivered { background: var(--success-tint); color: var(--success); }
    .ot-badge--cancelled { background: var(--danger-tint); color: var(--danger); }

    /* Stepper */
    .ot-stepper { position: relative; }
    .ot-stepper .ot-rail {
        position: absolute;
        top: 19px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: var(--line);
        z-index: 0;
    }
    .ot-stepper .ot-rail-fill {
        position: absolute;
        top: 19px;
        left: 5%;
        height: 2px;
        background: var(--success);
        z-index: 1;
        transition: width 0.4s ease;
    }
    .ot-step-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        border: 2px solid var(--line);
        background: var(--surface);
        color: var(--slate-soft);
        position: relative;
        z-index: 2;
    }
    .ot-step-dot--done {
        background: var(--success);
        border-color: var(--success);
        color: #fff;
    }
    .ot-step-dot--active {
        border-color: var(--brand);
        color: var(--brand);
        box-shadow: 0 0 0 4px var(--brand-tint);
    }
    .ot-step-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--slate-soft);
    }
    .ot-step-label--done { color: var(--ink); }
    .ot-step-date {
        font-size: 11.5px;
        color: var(--slate-soft);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* Status message strip */
    .ot-strip {
        border-left: 3px solid;
        border-radius: 8px;
        padding: 14px 18px;
        font-size: 14px;
        font-weight: 500;
    }
    .ot-strip--pending { background: var(--warning-tint); border-color: var(--warning); color: #8a5a08; }
    .ot-strip--confirmed, .ot-strip--processing { background: var(--info-tint); border-color: var(--info); color: #1a4fa3; }
    .ot-strip--shipped { background: var(--brand-tint); border-color: var(--brand); color: var(--brand-ink); }
    .ot-strip--delivered { background: var(--success-tint); border-color: var(--success); color: var(--success); }
    .ot-strip--cancelled { background: var(--danger-tint); border-color: var(--danger); color: var(--danger); }

    .ot-table th {
        font-family: 'Inter', sans-serif;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--slate-soft);
        background: var(--canvas);
    }
    .ot-table td { border-color: var(--line-soft); }
    .ot-table tbody tr:hover { background: var(--canvas); }

    .ot-thumb {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--line);
    }
    .ot-thumb-placeholder {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        background: var(--canvas);
        border: 1px dashed var(--line);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-soft);
        font-size: 10px;
        text-align: center;
    }

    .ot-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .ot-btn--primary { background: var(--brand); color: #fff; }
    .ot-btn--primary:hover { background: var(--brand-ink); }
    .ot-btn--ghost { background: var(--surface); color: var(--ink); border-color: var(--line); }
    .ot-btn--ghost:hover { background: var(--canvas); }
    .ot-btn--danger { background: var(--surface); color: var(--danger); border-color: #F0D3D1; }
    .ot-btn--danger:hover { background: var(--danger-tint); }
    .ot-btn--success { background: var(--success); color: #fff; }
    .ot-btn--success:hover { background: #0f6640; }
    .ot-btn:focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; }

    .ot-review-btn {
        background: var(--warning-tint);
        color: var(--warning);
        border: 1px solid #F0DDB3;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
    }
    .ot-review-btn:hover { background: #FBEACB; }

    @media print {
        .ot-no-print { display: none !important; }
    }
</style>

<div class="ot-page">
<div class="container mx-auto px-4 py-10 max-w-5xl">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--slate-soft)] mb-2">Order tracking</p>
            <h1 class="text-[28px] font-bold ot-display leading-tight">{{ $order->order_number }}</h1>
            <p class="text-sm text-[var(--slate)] mt-1.5">Placed on {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('H:i') }}</p>
        </div>
        <span class="ot-badge ot-badge--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
    </div>

    {{-- ── Progress stepper ───────────────────────────────────────────── --}}
    <div class="ot-card ot-shadow p-8 mb-6">
        <div class="ot-stepper flex items-start justify-between relative">
            @php
                $total = count($steps);
                $fillPercent = $total > 1 ? (min($currentStep, $total) - 1) / ($total - 1) * 90 : 0;
            @endphp
            <div class="ot-rail"></div>
            <div class="ot-rail-fill" style="width: {{ $fillPercent }}%"></div>

            @foreach($steps as $statusKey => $step)
                @php
                    $isCompleted = $step['index'] <= $currentStep;
                    $isActive = $step['index'] === $currentStep;
                @endphp
                <div class="flex flex-col items-center flex-1 gap-2.5">
                    <div class="ot-step-dot {{ $isCompleted ? 'ot-step-dot--done' : '' }} {{ $isActive && !$isCompleted ? 'ot-step-dot--active' : '' }}">
                        @if($isCompleted) ✓ @else {{ $step['index'] }} @endif
                    </div>
                    <p class="ot-step-label {{ $isCompleted ? 'ot-step-label--done' : '' }} text-center">{{ $step['label'] }}</p>
                    @if($isCompleted && in_array($statusKey, ['confirmed', 'shipped', 'delivered']))
                        <p class="ot-step-date">
                            @switch($statusKey)
                                @case('confirmed') {{ $order->confirmed_at?->format('M d') ?? '—' }} @break
                                @case('shipped') {{ $order->shipped_at?->format('M d') ?? '—' }} @break
                                @case('delivered') {{ $order->delivered_at?->format('M d') ?? '—' }} @break
                            @endswitch
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Status message ─────────────────────────────────────────────── --}}
    <div class="ot-strip ot-strip--{{ $order->status }} mb-8">
        @switch($order->status)
            @case('pending') ⏳ Your order has been placed and is awaiting payment confirmation. @break
            @case('confirmed') ✓ Payment verified — your order is being processed. @break
            @case('processing') 📦 Your order is being prepared for shipment. @break
            @case('shipped') 🚚 Your order is on its way. Track it with the provided carrier details. @break
            @case('delivered') ✓ Delivered. Thank you for shopping with us! @break
            @case('cancelled') ✕ This order has been cancelled. @break
            @default Your order is being processed.
        @endswitch
    </div>

    {{-- ── Items table ────────────────────────────────────────────────── --}}
    <div class="ot-card ot-shadow overflow-hidden mb-8">
        <table class="min-w-full ot-table">
            <thead>
                <tr>
                    <th class="px-6 py-3.5 text-left">Item</th>
                    <th class="px-6 py-3.5 text-left">Details</th>
                    <th class="px-6 py-3.5 text-center">Qty</th>
                    <th class="px-6 py-3.5 text-right">Price</th>
                    <th class="px-6 py-3.5 text-right">Total</th>
                    @if($order->status === 'delivered')
                        <th class="px-6 py-3.5 text-center">Review</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr class="border-b transition">
                    <td class="px-6 py-4">
                            
                                        @php
                                            $imgPath = $item->product->thumbnail ?? 
                                                       ($item->product->images ? json_decode($item->product->images, true)[0] ?? null : null);
                                        @endphp

                                        <img src="{{ $imgPath ? asset('storage/' . $imgPath) : asset('images/default-product.png') }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="product-thumb"
                                             onerror="this.src='{{ asset('images/default-product.png') }}';">
                                        
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-[var(--ink)]">{{ $item->product?->name ?? $item->product_name ?? 'Unknown Product' }}</div>
                        @if($item->color_name || $item->storage_capacity)
                            <div class="text-xs text-[var(--slate)] mt-1 flex items-center gap-1.5">
                                @if($item->color_name)<span>{{ $item->color_name }}</span>@endif
                                @if($item->color_name && $item->storage_capacity)<span class="text-[var(--line)]">•</span>@endif
                                @if($item->storage_capacity)<span>{{ $item->storage_capacity }}</span>@endif
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-semibold ot-mono">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-right text-sm font-medium ot-mono text-[var(--slate)]">{{ config('app.currency_symbol', '৳') }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold ot-mono">{{ config('app.currency_symbol', '৳') }}{{ number_format($item->subtotal, 2) }}</td>
                    @if($order->status === 'delivered')
                        <td class="px-6 py-4 text-center">
                            @if($item->product && in_array($item->product->id, $reviewedProductIds))
                                <span class="text-xs text-[var(--success)] font-semibold">✓ Reviewed</span>
                            @elseif($item->product)
                                <button
                                    type="button"
                                    onclick="document.getElementById('review-modal-{{ $item->product->id }}').classList.remove('hidden')"
                                    class="ot-review-btn whitespace-nowrap"
                                >
                                    ⭐ Write a review
                                </button>
                            @else
                                <span class="text-xs text-[var(--slate-soft)]">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="{{ $order->status === 'delivered' ? 6 : 5 }}" class="px-6 py-14 text-center text-[var(--slate-soft)] text-sm">No items in this order.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── Totals ──────────────────────────────────────────────────── --}}
        <div class="bg-[var(--canvas)] px-6 py-6 border-t border-[var(--line)]">
            <div class="flex flex-col justify-end max-w-sm ml-auto space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-[var(--slate)]">Subtotal</span>
                    <span class="font-semibold ot-mono">{{ config('app.currency_symbol', '৳') }}{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->shipping_fee > 0)
                <div class="flex justify-between">
                    <span class="text-[var(--slate)]">Shipping</span>
                    <span class="font-semibold ot-mono">{{ config('app.currency_symbol', '৳') }}{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="text-[var(--slate)]">Tax</span>
                    <span class="font-semibold ot-mono">{{ config('app.currency_symbol', '৳') }}{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="flex justify-between pb-2.5 border-b border-[var(--line)]">
                    <span class="text-[var(--slate)]">Discount</span>
                    <span class="font-semibold ot-mono text-[var(--success)]">-{{ config('app.currency_symbol', '৳') }}{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @else
                <div class="pb-2.5 border-b border-[var(--line)]"></div>
                @endif
                <div class="flex justify-between items-center pt-1">
                    <span class="text-base font-bold ot-display">Total</span>
                    <span class="text-lg font-bold ot-mono text-[var(--brand)]">{{ config('app.currency_symbol', '৳') }}{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Shipping + order info ──────────────────────────────────────── --}}
    @if($order->shipping_address)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="ot-card ot-shadow p-6">
            <h3 class="text-sm font-bold uppercase tracking-wide text-[var(--slate-soft)] mb-4">Shipping address</h3>
            <div class="text-sm text-[var(--ink)] space-y-1.5">
                <p class="font-semibold">{{ $order->shipping_name }}</p>
                <p class="text-[var(--slate)]">{{ $order->shipping_address }}</p>
                @if($order->shipping_city)<p class="text-[var(--slate)]">{{ $order->shipping_city }}{{ $order->shipping_province ? ', ' . $order->shipping_province : '' }}</p>@endif
                @if($order->shipping_postal_code)<p class="text-[var(--slate)]">{{ $order->shipping_postal_code }}</p>@endif
                @if($order->shipping_country)<p class="text-[var(--slate)]">{{ $order->shipping_country }}</p>@endif
                <div class="pt-3 mt-3 border-t border-[var(--line-soft)] space-y-1.5">
                    <p class="text-[var(--slate)]">📞 {{ $order->shipping_phone }}</p>
                    <p class="text-[var(--slate)]">✉️ {{ $order->shipping_email }}</p>
                </div>
            </div>
        </div>

        <div class="ot-card ot-shadow p-6">
            <h3 class="text-sm font-bold uppercase tracking-wide text-[var(--slate-soft)] mb-4">Order information</h3>
            <div class="text-sm space-y-3">
                <div class="flex justify-between">
                    <span class="text-[var(--slate)]">Order number</span>
                    <span class="font-semibold ot-mono">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[var(--slate)]">Order date</span>
                    <span class="font-semibold ot-mono">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[var(--slate)]">Status</span>
                    <span class="font-semibold capitalize">{{ $order->status }}</span>
                </div>
                @if($order->notes)
                <div class="pt-3 border-t border-[var(--line-soft)]">
                    <p class="text-[var(--slate)] mb-1.5">Notes</p>
                    <p class="text-[var(--ink)]">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── Actions ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 ot-no-print">
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-ink)] flex items-center gap-1.5">
            ← Back to orders
        </a>

        <div class="flex flex-wrap justify-center md:justify-end gap-2.5">
            @if($order->invoice)
                <a href="{{ route('orders.invoice.download', $order) }}" class="ot-btn ot-btn--ghost">📄 Download invoice</a>
            @endif

            <button onclick="window.print()" class="ot-btn ot-btn--ghost">🖨️ Print</button>

            @if(in_array($order->status, ['pending', 'confirmed']))
                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="ot-btn ot-btn--danger">✕ Cancel order</button>
                </form>
            @endif

            @if($order->status === 'pending')
                <a href="{{ route('orders.khqr.pay', $order) }}" class="ot-btn ot-btn--success">💳 Pay now</a>
            @endif
        </div>
    </div>
</div>
</div>

{{-- ── Review modals ─────────────────────────────────────────────────── --}}
@if($order->status === 'delivered')
    @foreach($order->items as $item)
        @if($item->product && ! in_array($item->product->id, $reviewedProductIds))
            <div id="review-modal-{{ $item->product->id }}" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative" style="font-family: 'Inter', sans-serif;">
                    <button
                        type="button"
                        onclick="document.getElementById('review-modal-{{ $item->product->id }}').classList.add('hidden')"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-lg leading-none"
                    >✕</button>

                    <h3 class="text-lg font-bold mb-1" style="font-family: 'Sora', sans-serif;">Review {{ $item->product->name }}</h3>
                    <p class="text-xs text-gray-500 mb-5">Share your experience with this product</p>

                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reviews.store', $item->product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Rating</label>
                            <select name="rating" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#2952E3] focus:border-[#2952E3]">
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ $i == 5 ? 'selected' : '' }}>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Title (optional)</label>
                            <input type="text" name="title" maxlength="150" class="w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#2952E3] focus:border-[#2952E3]" placeholder="Sum up your experience">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your review (optional)</label>
                            <textarea name="body" maxlength="2000" rows="4" class="w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#2952E3] focus:border-[#2952E3]" placeholder="What did you like or dislike?"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Photos (optional, up to 5)</label>
                            <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm">
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button
                                type="button"
                                onclick="document.getElementById('review-modal-{{ $item->product->id }}').classList.add('hidden')"
                                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-semibold"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg text-white transition text-sm font-semibold" style="background:#2952E3" onmouseover="this.style.background='#1E3FBF'" onmouseout="this.style.background='#2952E3'">
                                Submit review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection