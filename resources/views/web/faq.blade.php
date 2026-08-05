@extends('layouts.app')

@section('title', 'FAQ')
@section('page-title', 'Frequently Asked Questions')

@section('content')

<div class="max-w-[820px] mx-auto px-4 sm:px-6 py-10 sm:py-14">

    {{-- Header --}}
    <div class="mb-8 sm:mb-10">
        <div class="inline-flex items-center gap-2 text-xs font-bold tracking-wide uppercase text-[#1562c7] mb-3">
            <i class="fa-solid fa-circle-question"></i> Help center
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#10336B] mb-3">Frequently asked questions</h1>
        <p class="text-sm sm:text-[15px] text-[#4F5D77] leading-relaxed max-w-[560px]">
            Quick answers about ordering, delivery, warranty, and your account. Can't find
            what you need? Reach out below.
        </p>
    </div>

    {{-- Search --}}
    <div class="relative mb-9 sm:mb-11">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[#adadad] text-sm"></i>
        <input type="text" id="faq-search" placeholder="Search questions..."
               class="w-full text-sm border border-[#e5e7eb] rounded-full py-3 pl-11 pr-4
                      focus:outline-none focus:border-[#1562c7] transition-colors">
    </div>

    <div id="faq-empty" class="hidden text-center text-sm text-[#4F5D77] py-10">
        No questions matched your search.
    </div>

    @php
        $faqGroups = [
            'Orders & payment' => [
                ['q' => 'What payment methods do you accept?', 'a' => 'We accept cash on delivery, bank transfer, and card payment at checkout. Cash on delivery is available in USD or KHR.'],
                ['q' => 'Can I change or cancel my order after placing it?', 'a' => 'You can request a change or cancellation before your order is dispatched. Once it has shipped, it can no longer be modified — but you can still return it under our warranty terms if eligible.'],
                ['q' => 'Do you sell brand-new phones only, or refurbished too?', 'a' => 'We carry both. Refurbished and pre-owned devices are clearly labeled on the product page and carry a shorter warranty period.'],
            ],
            'Shipping & delivery' => [
                ['q' => 'How long does delivery take?', 'a' => 'Same day to 1 business day within Phnom Penh, 1–2 days for major provinces, and 2–4 days for remote provinces. See our Shipping Policy for full details.'],
                ['q' => 'Do you deliver outside Phnom Penh?', 'a' => 'Yes, we deliver to all 25 provinces and municipalities in Cambodia.'],
                ['q' => 'How do I track my order?', 'a' => 'You\'ll receive a tracking link by SMS or Telegram once your order ships. You can also check the status anytime from My Orders.'],
            ],
            'Warranty & returns' => [
                ['q' => 'What\'s covered under warranty?', 'a' => 'Manufacturing defects and hardware faults under normal use. Physical or liquid damage, and unauthorized repairs, are not covered. Full details are in our Warranty Policy.'],
                ['q' => 'How long is the warranty period?', 'a' => 'New phones come with 12 months, refurbished phones with 3 months, and accessories like chargers and power banks with 6 months.'],
                ['q' => 'How do I start a warranty claim?', 'a' => 'Contact our support team with your order number, or bring the device to our store. We\'ll inspect it and repair, replace, or refund as appropriate.'],
            ],
            'Account & support' => [
                ['q' => 'Do I need an account to place an order?', 'a' => 'You can check out as a guest, but creating an account lets you track orders, save your wishlist, and speed up future checkouts.'],
                ['q' => 'How do I contact support?', 'a' => 'Use the Contact Us page, or message us directly on Telegram. Our team typically replies within a few hours.'],
                ['q' => 'How do I reset my password?', 'a' => 'Go to the sign-in page and select "Forgot password" to receive a reset link by email or SMS.'],
            ],
        ];
    @endphp

    <div id="faq-list">
        @foreach($faqGroups as $group => $items)
            <div class="faq-group mb-8 sm:mb-10">
                <h2 class="text-sm font-bold text-[#10336B] uppercase tracking-wide mb-3">{{ $group }}</h2>
                <div class="rounded-[10px] border border-[#e5e7eb] divide-y divide-[#e5e7eb] overflow-hidden bg-white">
                    @foreach($items as $item)
                        <details class="faq-item group px-5">
                            <summary class="py-4 flex items-center justify-between gap-4 cursor-pointer list-none">
                                <span class="faq-q text-sm font-semibold text-[#2B3D5D]">{{ $item['q'] }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-[#adadad] shrink-0 transition-transform group-open:rotate-180"></i>
                            </summary>
                            <p class="faq-a text-sm text-[#4F5D77] leading-relaxed pb-4 pr-7">{{ $item['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Contact strip --}}
    <div class="rounded-[10px] border border-[#e5e7eb] bg-[#F8F9FB] p-6 flex flex-col sm:flex-row
                sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-[#10336B] mb-0.5">Still have a question?</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('faq-search');
    const groups = document.querySelectorAll('.faq-group');
    const emptyState = document.getElementById('faq-empty');

    searchInput.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();
        let anyVisible = false;

        groups.forEach(function (group) {
            let groupHasMatch = false;

            group.querySelectorAll('.faq-item').forEach(function (item) {
                const q = item.querySelector('.faq-q').textContent.toLowerCase();
                const a = item.querySelector('.faq-a').textContent.toLowerCase();
                const matches = term === '' || q.includes(term) || a.includes(term);

                item.style.display = matches ? '' : 'none';
                if (matches) {
                    groupHasMatch = true;
                    if (term !== '') item.setAttribute('open', '');
                    else item.removeAttribute('open');
                }
            });

            group.style.display = groupHasMatch ? '' : 'none';
            if (groupHasMatch) anyVisible = true;
        });

        emptyState.classList.toggle('hidden', anyVisible);
    });
});
</script>
@endpush

@endsection