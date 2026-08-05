@extends('layouts.app')

@section('title', 'Terms & Conditions')
@section('page-title', 'Terms & Conditions')

@section('content')

<div class="max-w-[900px] mx-auto px-4 sm:px-6 py-10 sm:py-14">

    {{-- Header --}}
    <div class="mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 text-xs font-bold tracking-wide uppercase text-[#1562c7] mb-3">
            <i class="fa-solid fa-file-contract"></i> Terms of use
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#10336B] mb-3">Terms &amp; conditions</h1>
        <p class="text-sm sm:text-[15px] text-[#4F5D77] leading-relaxed max-w-[620px]">
            The rules for using our site and buying from us. By placing an order, you agree
            to the terms below.
        </p>
        <p class="text-xs text-[#adadad] mt-3">Last updated: {{ $lastUpdated ?? 'January 1, 2026' }}</p>
    </div>

    {{-- Using the site --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Using our site</h2>
                <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                    <li>You must be at least 18, or have a parent or guardian's permission, to place an order</li>
                    <li>The information you give us at checkout must be accurate and complete</li>
                    <li>You're responsible for keeping your account login private</li>
                    <li>You agree not to use the site for any unlawful purpose or to disrupt its operation</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Orders & pricing --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DFF3E7] text-[#0F6E56]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Orders and pricing</h2>
                <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                    <li>All prices are shown in USD unless stated otherwise</li>
                    <li>We reserve the right to correct pricing errors, even after an order is placed</li>
                    <li>Placing an order is an offer to buy; we confirm it once your order is processed</li>
                    <li>We may cancel an order if a product is out of stock or pricing was listed incorrectly, and will refund any payment already made</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Payment --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10 sm:mb-12">
        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">Payment</h2>
                    <p class="text-sm text-[#4F5D77] leading-relaxed">
                        We accept cash on delivery, bank transfer, and card payment. Full payment
                        is required before dispatch, except for cash-on-delivery orders.
                    </p>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#EDE7FB] text-[#3C3489]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">Returns &amp; warranty</h2>
                    <p class="text-sm text-[#4F5D77] leading-relaxed">
                        Product warranty is covered separately in our
                        <a href="{{ route('warranty-policy') }}" class="text-[#1562c7] font-semibold hover:underline">Warranty Policy</a>.
                    </p>
                </div>
            </div>
        </section>
    </div>

    {{-- Intellectual property --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-copyright"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Intellectual property</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    All content on this site — text, images, logos, and design — belongs to
                    PhoneByAshahi or our suppliers, and may not be copied or reused without
                    permission. Product names and brand logos remain the property of their
                    respective owners.
                </p>
            </div>
        </div>
    </section>

    {{-- Liability --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Limitation of liability</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    We aim for our site and product listings to be accurate, but we don't
                    guarantee they're free of errors. To the extent permitted by law, we're not
                    liable for indirect or incidental losses arising from your use of the site
                    or products purchased through it.
                </p>
            </div>
        </div>
    </section>

    {{-- Governing law --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DFF3E7] text-[#0F6E56]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-landmark"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Governing law</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    These terms are governed by the laws of the Kingdom of Cambodia. Any disputes
                    will be handled in accordance with Cambodian law.
                </p>
            </div>
        </div>
    </section>

    {{-- Changes --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#EDE7FB] text-[#3C3489]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-circle-exclamation"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Changes to these terms</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    We may update these terms from time to time. Continuing to use the site after
                    changes are posted means you accept the updated terms.
                </p>
            </div>
        </div>
    </section>

    {{-- Contact strip --}}
    <div class="rounded-[10px] border border-[#e5e7eb] bg-[#F8F9FB] p-6 flex flex-col sm:flex-row
                sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-[#10336B] mb-0.5">Questions about these terms?</p>
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