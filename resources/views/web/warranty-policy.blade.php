@extends('layouts.app')

@section('title', 'Warranty Policy')
@section('page-title', 'Warranty Policy')

@section('content')

<div class="max-w-[900px] mx-auto px-4 sm:px-6 py-10 sm:py-14">

    {{-- Header --}}
    <div class="mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 text-xs font-bold tracking-wide uppercase text-[#1562c7] mb-3">
            <i class="fa-solid fa-shield-halved"></i> Warranty information
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#10336B] mb-3">Warranty policy</h1>
        <p class="text-sm sm:text-[15px] text-[#4F5D77] leading-relaxed max-w-[620px]">
            What's covered, for how long, and how to make a claim if something you
            bought from us stops working the way it should.
        </p>
    </div>

    {{-- Coverage by product type --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Coverage by product type</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    Warranty length depends on what you bought. Coverage starts from the delivery date.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-[10px] border border-[#e5e7eb]">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-left text-[#10336B]">
                        <th class="py-3 px-4 font-bold">Product</th>
                        <th class="py-3 px-4 font-bold">Warranty period</th>
                        <th class="py-3 px-4 font-bold">Covered by</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb]">
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">New smartphones</td>
                        <td class="py-3 px-4 text-[#4F5D77]">12 months</td>
                        <td class="py-3 px-4 text-[#4F5D77]">Manufacturer + PhoneByAshahi</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">Pre-owned / refurbished phones</td>
                        <td class="py-3 px-4 text-[#4F5D77]">3 months</td>
                        <td class="py-3 px-4 text-[#4F5D77]">PhoneByAshahi</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">Chargers, power banks, cables</td>
                        <td class="py-3 px-4 text-[#4F5D77]">6 months</td>
                        <td class="py-3 px-4 text-[#4F5D77]">PhoneByAshahi</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-[#2B3D5D] font-semibold">Cases, screen protectors, holders</td>
                        <td class="py-3 px-4 text-[#4F5D77]">Not covered</td>
                        <td class="py-3 px-4 text-[#4F5D77]">—</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- What's covered vs not --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10 sm:mb-12">
        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#DFF3E7] text-[#0F6E56]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">What's covered</h2>
                    <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                        <li>Manufacturing defects and hardware faults</li>
                        <li>Battery that drops below 80% health within the period</li>
                        <li>Charging port, button, or screen faults under normal use</li>
                    </ul>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">What's not covered</h2>
                    <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                        <li>Physical or liquid damage, cracked screens</li>
                        <li>Unauthorized repairs or opened devices</li>
                        <li>Devices with tampered or removed serial numbers</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    {{-- How to claim --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#EDE7FB] text-[#3C3489]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">How to make a claim</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    Bring your device to our store or send it in with proof of purchase — your order
                    number from
                    <a href="{{ route('orders.index') }}" class="text-[#1562c7] font-semibold hover:underline">My Orders</a>
                    works as proof.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-[10px] border border-[#e5e7eb] p-4">
                <div class="text-xs font-bold text-[#adadad] mb-1">Step 1</div>
                <p class="text-sm text-[#2B3D5D] font-semibold">Contact support</p>
                <p class="text-xs text-[#4F5D77] mt-1">Tell us the issue and your order number.</p>
            </div>
            <div class="rounded-[10px] border border-[#e5e7eb] p-4">
                <div class="text-xs font-bold text-[#adadad] mb-1">Step 2</div>
                <p class="text-sm text-[#2B3D5D] font-semibold">We inspect the device</p>
                <p class="text-xs text-[#4F5D77] mt-1">In-store or after you send it to us.</p>
            </div>
            <div class="rounded-[10px] border border-[#e5e7eb] p-4">
                <div class="text-xs font-bold text-[#adadad] mb-1">Step 3</div>
                <p class="text-sm text-[#2B3D5D] font-semibold">Repair, replace, or refund</p>
                <p class="text-xs text-[#4F5D77] mt-1">Usually within 5–7 business days.</p>
            </div>
        </div>
    </section>

    {{-- Contact strip --}}
    <div class="rounded-[10px] border border-[#e5e7eb] bg-[#F8F9FB] p-6 flex flex-col sm:flex-row
                sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-[#10336B] mb-0.5">Need to start a warranty claim?</p>
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