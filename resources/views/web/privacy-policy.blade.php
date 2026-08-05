@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('page-title', 'Privacy Policy')

@section('content')

<div class="max-w-[900px] mx-auto px-4 sm:px-6 py-10 sm:py-14">

    {{-- Header --}}
    <div class="mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 text-xs font-bold tracking-wide uppercase text-[#1562c7] mb-3">
            <i class="fa-solid fa-lock"></i> Privacy information
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#10336B] mb-3">Privacy policy</h1>
        <p class="text-sm sm:text-[15px] text-[#4F5D77] leading-relaxed max-w-[620px]">
            What information we collect when you shop with us, how we use it, and the
            choices you have.
        </p>
        <p class="text-xs text-[#adadad] mt-3">Last updated: {{ $lastUpdated ?? 'January 1, 2026' }}</p>
    </div>

    {{-- Information we collect --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Information we collect</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed mb-2">
                    When you create an account, place an order, or contact us, we collect:
                </p>
                <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                    <li>Name, phone number, and delivery address</li>
                    <li>Email address, if provided</li>
                    <li>Order history and payment method used (we do not store full card numbers)</li>
                    <li>Basic device and browser information, for security and site performance</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- How we use it --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DFF3E7] text-[#0F6E56]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-gears"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">How we use your information</h2>
                <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                    <li>To process and deliver your orders</li>
                    <li>To send order updates by SMS, Telegram, or email</li>
                    <li>To respond to warranty claims and support requests</li>
                    <li>To improve our products, site, and customer service</li>
                </ul>
                <p class="text-sm text-[#4F5D77] leading-relaxed mt-2">
                    We do not sell your personal information to third parties.
                </p>
            </div>
        </div>
    </section>

    {{-- Who we share with --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-share-nodes"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Who we share information with</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed mb-2">
                    We share only what's needed to fulfil your order, with:
                </p>
                <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                    <li>Delivery partners, to get your order to you (see our <a href="{{ route('shipping-policy') }}" class="text-[#1562c7] font-semibold hover:underline">Shipping Policy</a>)</li>
                    <li>Payment providers, to process card or bank transfer payments</li>
                    <li>Authorities, if required by Cambodian law</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Cookies --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10 sm:mb-12">
        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#EDE7FB] text-[#3C3489]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-cookie-bite"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">Cookies</h2>
                    <p class="text-sm text-[#4F5D77] leading-relaxed">
                        We use cookies to keep you signed in, remember your cart, and understand how
                        the site is used. You can disable cookies in your browser, though some
                        features may not work correctly without them.
                    </p>
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 shrink-0 rounded-full bg-[#DCEAFB] text-[#0C447C]
                            flex items-center justify-center text-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#10336B] mb-1.5">Data security</h2>
                    <p class="text-sm text-[#4F5D77] leading-relaxed">
                        We use reasonable technical and organizational measures to protect your
                        information. No online system is completely secure, but we work to keep
                        your data safe.
                    </p>
                </div>
            </div>
        </section>
    </div>

    {{-- Your rights --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#DFF3E7] text-[#0F6E56]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Your choices</h2>
                <ul class="text-sm text-[#4F5D77] leading-relaxed space-y-1.5 list-disc pl-4">
                    <li>You can view and update your details anytime from your <a href="{{ route('profile.index') }}" class="text-[#1562c7] font-semibold hover:underline">Profile</a></li>
                    <li>You can ask us to delete your account and personal data</li>
                    <li>You can opt out of marketing messages at any time</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Changes to policy --}}
    <section class="mb-10 sm:mb-12">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#FBE0E3] text-[#993556]
                        flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-circle-exclamation"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[#10336B] mb-1.5">Changes to this policy</h2>
                <p class="text-sm text-[#4F5D77] leading-relaxed">
                    We may update this policy from time to time. If we make significant changes,
                    we'll let you know by posting a notice on this page with an updated date.
                </p>
            </div>
        </div>
    </section>

    {{-- Contact strip --}}
    <div class="rounded-[10px] border border-[#e5e7eb] bg-[#F8F9FB] p-6 flex flex-col sm:flex-row
                sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-[#10336B] mb-0.5">Questions about your data?</p>
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