@extends('layouts.app')

@section('content')

<div class="max-w-md mx-auto px-4 sm:px-6 py-8 sm:py-12">

    @php
        $icons = [
            'card' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><path d="M6.5 14.5h4"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>',
            'check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M5 12.5l4.5 4.5L19 7"/></svg>',
            'cross' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M6 6l12 12M18 6L6 18"/></svg>',
            'arrow-left' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full"><path d="M19 12H5"/><path d="M11 6l-6 6 6 6"/></svg>',
        ];
        $icon = fn ($name, $class = 'w-4 h-4') => '<span class="inline-block '.$class.' align-[-3px]">'.$icons[$name].'</span>';
    @endphp

    {{-- ── HEADER ── --}}
    <div class="text-center mb-6">
        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
            {!! $icon('card', 'w-6 h-6') !!}
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Scan to Pay</h1>
        <p class="text-sm text-gray-500 mt-1">Order {{ $order->order_number }}</p>
    </div>

    {{-- ── QR CARD ── --}}
    <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 text-center">

        <div class="text-3xl font-bold text-gray-800 mb-1">
            {{ config('app.currency_symbol', '$') }}{{ number_format($order->total, 2) }}
        </div>
        <p class="text-xs text-gray-500 mb-6">Total amount due</p>

        {{-- QR renders here via JS --}}
        <div id="qr-wrapper" class="flex items-center justify-center mb-5">
            <div id="qrcode" class="inline-block p-3 bg-white border rounded-lg"></div>
        </div>

        {{-- Expired overlay state --}}
        <div id="qr-expired" class="hidden mb-5">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-3">
                {!! $icon('cross', 'w-7 h-7') !!}
            </div>
            <p class="text-sm font-semibold text-gray-800">This QR code has expired</p>
            <p class="text-xs text-gray-500 mt-1 mb-4">Refresh the page to generate a new one.</p>
            <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm font-semibold">
                Generate new code
            </button>
        </div>

        {{-- Countdown --}}
        <div id="qr-timer" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-full">
            {!! $icon('clock', 'w-3.5 h-3.5') !!}
            <span>Expires in <span id="qr-countdown">--:--</span></span>
        </div>

        <p class="text-xs text-gray-400 mt-5 leading-relaxed">
            Open your banking app (ABA, ACLEDA, Wing, or any Bakong-linked app) and scan this code to complete payment.
        </p>
    </div>

    {{-- ── ACTIONS ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-6">
        <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5">
            {!! $icon('arrow-left', 'w-3.5 h-3.5') !!} Back to order
        </a>
        <a href="{{ route('orders.show', $order) }}" class="w-full sm:w-auto text-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm font-semibold">
            {!! $icon('check', 'w-3.5 h-3.5') !!} I've completed payment
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    (function () {
        var qrText = @json($qrString);
        var expiresAt = @json($expiresAt); // ISO 8601 string or null

        // ── Render QR from the raw KHQR string ──
        if (qrText) {
            new QRCode(document.getElementById('qrcode'), {
                text: qrText,
                width: 220,
                height: 220,
                correctLevel: QRCode.CorrectLevel.M
            });
        } else {
            document.getElementById('qr-wrapper').innerHTML =
                '<p class="text-sm text-red-500">Unable to load QR code.</p>';
        }

        // ── Countdown to expiry ──
        if (expiresAt) {
            var target = new Date(expiresAt).getTime();

            var tick = function () {
                var diff = target - Date.now();

                if (diff <= 0) {
                    document.getElementById('qr-wrapper').classList.add('hidden');
                    document.getElementById('qr-timer').classList.add('hidden');
                    document.getElementById('qr-expired').classList.remove('hidden');
                    clearInterval(timer);
                    return;
                }

                var totalSeconds = Math.floor(diff / 1000);
                var minutes = Math.floor(totalSeconds / 60);
                var seconds = totalSeconds % 60;
                document.getElementById('qr-countdown').textContent =
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            };

            tick();
            var timer = setInterval(tick, 1000);
        } else {
            document.getElementById('qr-timer').classList.add('hidden');
        }

        // ── Optional: poll a status endpoint to auto-redirect once paid ──
        // Wire this up once you have a route that checks payment status, e.g.:
        //
        // var statusUrl = '{{ url("/orders") }}/{{ $order->id }}/pay/khqr/status';
        // setInterval(function () {
        //     fetch(statusUrl).then(r => r.json()).then(function (data) {
        //         if (data.paid) {
        //             window.location.href = '{{ route("orders.show", $order) }}';
        //         }
        //     });
        // }, 5000);
    })();
</script>
@endpush

@endsection
