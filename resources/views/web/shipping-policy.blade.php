@extends('layouts.app')

@section('title', 'Shipping Policy')
@section('page-title', 'Shipping Policy')

@section('content')

<div class="max-w-[900px] mx-auto px-4 sm:px-6 py-10 sm:py-14">

    {{-- Header --}}
    <div class="mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 text-xs font-bold tracking-wide uppercase text-[#1562c7] mb-3">
            <i class="fa-solid fa-truck-fast"></i> Delivery information
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#10336B] mb-3">Shipping policy</h1>
        <p class="text-sm sm:text-[15px] text-[#4F5D77] leading-relaxed max-w-[620px]">
            How we get your order to you anywhere in Cambodia — delivery times, fees,
            and what happens if something goes wrong along the way.
        </p>
    </div>

    {{-- Processing time --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                        flex items-center justify-center text-lg">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Order processing time</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    Orders placed before 3:00 PM on a business day are processed and handed to our
                    delivery partner the same day. Orders placed after 3:00 PM, or on Sundays and
                    public holidays, are processed the next business day.
                </p>
            </div>
        </div>
    </section>

    {{-- Delivery zones & fees --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DFF3E7] text-[#0F6E56]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Delivery zones and fees</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    We deliver to all 25 provinces and municipalities in Cambodia.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-[10px] border border-[#e5e7eb]">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-left text-[#10336B]">
                        <th class="py-3 px-4 font-bold">Zone</th>
                        <th class="py-3 px-4 font-bold">Estimated time</th>
                        <th class="py-3 px-4 font-bold">Delivery fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb]">
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">Phnom Penh</td>
                        <td class="py-3 px-4 text-[#4F5D77]">Same day – 1 business day</td>
                        <td class="py-3 px-4 text-[#4F5D77]">$1.50, free over $50</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">Major provinces</td>
                        <td class="py-3 px-4 text-[#4F5D77]">1 – 2 business days</td>
                        <td class="py-3 px-4 text-[#4F5D77]">$2.50, free over $75</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">Remote provinces</td>
                        <td class="py-3 px-4 text-[#4F5D77]">2 – 4 business days</td>
                        <td class="py-3 px-4 text-[#4F5D77]">$3.50, free over $100</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-[#adadad] mt-2.5">
            Major provinces: Siem Reap, Battambang, Kampong Cham, Sihanoukville, Kandal.
            All other provinces are considered remote.
        </p>
    </section>

    {{-- Delivery partners + COD --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10 sm:mb-12">
        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">Delivery partners</h2>
                    <p class="text-sm text-[#4F5D77] leading-relaxed">
                        Orders are shipped through J&amp;T Express, Virak Buntham, or our own riders
                        in Phnom Penh, depending on your location and item size.
                    </p>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#EDE7FB] text-[#3C3489]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">Cash on delivery</h2>
                    <p class="text-sm text-[#4F5D77] leading-relaxed">
                        Cash on delivery (COD) is available nationwide in USD or KHR at the daily
                        exchange rate. Bank transfer and card payment are also accepted at checkout.
                    </p>
                </div>
            </div>
        </section>
    </div>

    {{-- Tracking --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-magnifying-glass-location"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Tracking your order</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    You'll get a tracking link by SMS or Telegram once your order ships. You can also
                    check delivery status anytime from
                    <a href="{{ route('orders.index') }}" class="text-[#1562c7] font-semibold hover:underline">My Orders</a>.
                </p>
            </div>
        </div>
    </section>

    {{-- Delays / issues --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Delayed or lost packages</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    If your order hasn't arrived within the estimated window, contact us and we'll
                    trace it with the delivery partner. If a package is confirmed lost in transit,
                    we'll send a replacement or issue a full refund — whichever you prefer.
                </p>
            </div>
        </div>
    </section>

    {{-- Contact strip --}}
    <div class="rounded-[10px] border border-[#e5e7eb] bg-[#F8F9FB] p-6 flex flex-col sm:flex-row
                sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-[#10336B] mb-0.5">Questions about your delivery?</p>
            <p class="text-sm text-[#4F5D77]">Our team replies within a few hours, every day.</p>
        </div>
        <a href="{{ route('contact.index') }}"
           class="inline-flex items-center justify-center gap-2 bg-[#10336B] text-white
                  rounded-full px-5 py-2.5 text-sm font-bold shrink-0
                  transition-transform duration-200 hover:-translate-y-0.5">
            Contact us <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

</div>

@endsection