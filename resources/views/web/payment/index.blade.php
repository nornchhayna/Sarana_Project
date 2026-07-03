@extends('layouts.app')
@section('title', 'Complete Payment — ' . $order->order_number)

@section('content')
<div class="payment-page">
    <div class="payment-wrapper">

        {{-- ── Left: QR Card ── --}}
        <div class="qr-card">

            {{-- Header --}}
            <div class="qr-card-header">
                <img src="{{ asset('images/bakong-logo.png') }}" alt="Bakong" class="bakong-logo"
                     onerror="this.style.display='none'">
                <div>
                    <h2 class="qr-title">Scan to Pay</h2>
                    <p class="qr-sub">Use any Bakong-compatible banking app</p>
                </div>
            </div>

            {{-- Amount --}}
            <div class="qr-amount-block">
                <span class="qr-amount-label">Amount Due</span>
                <span class="qr-amount">${{ number_format($order->total, 2) }}</span>
            </div>

            {{-- QR Code --}}
            <div class="qr-image-wrap" id="qr-wrap">
                @php
                    $qrString = (isset($khqrData['success']) && $khqrData['success'] && !empty($khqrData['qr_string']))
                        ? $khqrData['qr_string']
                        : null;
                @endphp

                @if($qrString)
                    {{-- QR renders here via JS --}}
                    <div id="qr-container">
                        <div id="qrcode"></div>
                        <div id="qr-loading" class="qr-loading-text">Generating QR…</div>
                    </div>
                    <input type="hidden" id="qr-string" value="{{ trim($qrString) }}">
                @else
                    <div class="qr-unavailable">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                             stroke="#b0b0be" stroke-width="1.5">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <path d="M14 14h2v2h-2zM18 14h3M14 18h2M18 18h3v3M16 20v1"/>
                        </svg>
                        <p>QR not available.<br>
                           @if(!empty($khqrData['error']))
                               <small style="color:#e74c3c">{{ $khqrData['error'] }}</small>
                           @else
                               Please add your Bakong account ID to <code>.env</code>.
                           @endif
                        </p>
                    </div>
                @endif

                {{-- Paid overlay (shown by JS on confirmation) --}}
                <div class="qr-paid-overlay" id="paid-overlay" style="display:none">
                    <div class="paid-icon">✓</div>
                    <p class="paid-label">Payment confirmed!</p>
                    <p class="paid-sub">Thank you for your purchase.</p>
                </div>
            </div>

            {{-- Full-page success banner --}}
            <div class="payment-success-banner" id="success-banner" style="display:none">
                <div class="success-banner-icon">✓</div>
                <h3 class="success-banner-title">Payment Successful!</h3>
                <p class="success-banner-text">
                    Your payment of <strong>${{ number_format($order->total, 2) }}</strong> was received.
                    Redirecting you to shop more products…
                </p>
            </div>

            {{-- Status indicator --}}
            <div class="status-row" id="status-row">
                <span class="status-dot pending" id="status-dot"></span>
                <span class="status-text" id="status-text">Waiting for payment…</span>
                <span class="poll-timer" id="poll-timer"></span>
            </div>

            {{-- Order ref --}}
            <div class="order-ref">
                <span class="order-ref-label">Order</span>
                <span class="order-ref-number">{{ $order->order_number }}</span>
            </div>

            {{-- Countdown --}}
            <p class="qr-expiry">
                QR code valid for <span id="countdown">30:00</span>
            </p>

            {{-- ── KHQR Debug Panel (remove or hide behind an env check in production) ── --}}
            @if(config('app.debug'))
            <div class="khqr-debug" id="khqr-debug">
                <button type="button" class="khqr-debug-toggle" id="khqr-debug-toggle">
                    🔍 KHQR Debug Info <span id="khqr-debug-status-pill"></span>
                </button>
                <div class="khqr-debug-body" id="khqr-debug-body" style="display:none">
                    <p class="khqr-debug-hint">
                        This panel decodes the raw KHQR string and checks its structure and CRC checksum.
                        It only appears when <code>APP_DEBUG=true</code>. Remove the
                        <code>@@if(config('app.debug'))</code> wrapper (or set <code>APP_DEBUG=false</code>)
                        before going to production.
                    </p>
                    <div id="khqr-debug-output">Decoding…</div>
                </div>
            </div>
            @endif

            {{-- Steps --}}
            <ol class="how-to">
                <li>Open your banking app (ABA, ACLEDA, Wing, etc.)</li>
                <li>Tap <strong>Scan QR</strong> or <strong>Pay</strong></li>
                <li>Scan the code above and confirm the amount</li>
                <li>This page updates automatically once payment is received</li>
            </ol>

            {{-- Actions --}}
            <div class="qr-actions">
                <button type="button" class="btn-cancel-payment" id="btn-cancel-payment">Cancel Payment</button>
                <a href="{{ route('products.index') }}" class="btn-continue-shop" id="btn-continue-shop">Continue Shopping</a>
            </div>
        </div>

        {{-- ── Right: Order Summary ── --}}
        <aside class="order-summary-card">
            <h3 class="summary-heading">Order Summary</h3>

            <div class="summary-meta">
                <span class="meta-label">Order number</span>
                <span class="meta-value">{{ $order->order_number }}</span>
            </div>
            <div class="summary-meta">
                <span class="meta-label">Date</span>
                <span class="meta-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="summary-meta">
                <span class="meta-label">Ship to</span>
                <span class="meta-value">{{ $order->shipping_name }}, {{ $order->shipping_city }}</span>
            </div>

            <div class="summary-divider"></div>

            @foreach($order->items as $item)
            <div class="summary-item">
                <div class="item-info">
                    <span class="item-name">{{ $item->product_name }}</span>
                    @if($item->color_name || $item->storage_label)
                        <span class="item-variant">
                            {{ $item->color_name }}
                            @if($item->color_name && $item->storage_label) · @endif
                            {{ $item->storage_label }}
                        </span>
                    @endif
                    <span class="item-qty">Qty: {{ $item->quantity }}</span>
                </div>
                <span class="item-price">
                    ${{ number_format($item->total_price ?? $item->subtotal ?? ($item->unit_price * $item->quantity), 2) }}
                </span>
            </div>
            @endforeach

            <div class="summary-divider"></div>

            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>

            @if($order->discount_amount > 0)
            <div class="total-row discount-row">
                <span>Discount</span>
                <span>-${{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif

            <div class="total-row">
                <span>Shipping</span>
                <span class="free-tag">Free</span>
            </div>

            <div class="summary-divider"></div>

            <div class="total-row grand-total">
                <span>Total</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>

            {{-- Payment status badge --}}
            <div class="payment-status-badge" id="payment-badge">
                <span class="badge-dot" id="badge-dot"></span>
                <span id="badge-text">Awaiting Payment</span>
            </div>
        </aside>

    </div>
</div>

{{-- Hidden meta for JS --}}
<div id="redirect-url"
     data-poll="{{ route('checkout.payment.status', $order) }}"
     data-cancel="{{ route('checkout.payment.cancel', $order) }}"
     data-success="{{ route('products.index') }}"
     data-csrf="{{ csrf_token() }}"
     style="display:none"></div>

<style>

</style>

<script>
(function () {
    'use strict';

    var meta       = document.getElementById('redirect-url');
    var pollUrl    = meta.dataset.poll;
    var cancelUrl  = meta.dataset.cancel;
    var successUrl = meta.dataset.success;
    var csrfToken  = meta.dataset.csrf;
    var paymentDone = false;

    // ── QR Code generation ──────────────────────────────────────────────────
    // NOTE: We deliberately do NOT use the "qrcodejs" (davidshimjs) library.
    // It has a long-standing, well-documented bug where strings roughly
    // 190-220 characters long combined with error-correction level H throw
    // a "code length overflow" error — and KHQR strings land in exactly that
    // range. Depending on browser/timing, this can leave a corrupted-looking
    // QR matrix on screen that *looks* like a QR code but isn't decodable by
    // anything, which is exactly the "scans but nothing happens" symptom.
    // "qrcode-generator" handles arbitrary lengths correctly.
    var qrInput = document.getElementById('qr-string');

    if (qrInput && qrInput.value.trim()) {
        var script   = document.createElement('script');
        script.src   = 'https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js';
        script.async = true;

      // Replace the existing script.onload logic with this more robust version:
script.onload = function () {
    var target = document.getElementById('qrcode');
    target.innerHTML = ''; // Clear previous attempts
    
    var qrText = qrInput.value.trim();
    var qr = qrcode(0, 'M'); 
    qr.addData(qrText);
    qr.make();

    // Use createSvgTag if you want perfect crispness
    // createSvgTag(cellSize, margin)
    target.innerHTML = qr.createSvgTag(6, 4); 
    
    var svg = target.querySelector('svg');
    if (svg) {
        svg.style.width = '224px';
        svg.style.height = '224px';
    }
};

        script.onerror = function () {
            var loading = document.getElementById('qr-loading');
            if (loading) {
                loading.textContent = 'Could not load QR library.';
                loading.style.color = '#e74c3c';
            }
        };

        document.head.appendChild(script);
    }

    // ── Countdown (30 min = 1800 s, matches KHQR expiry) ───────────────────
    var expirySeconds = 30 * 60;
    var countdownEl   = document.getElementById('countdown');

    var countdownTimer = setInterval(function () {
        expirySeconds--;
        if (expirySeconds <= 0) {
            clearInterval(countdownTimer);
            if (countdownEl) countdownEl.textContent = '00:00';
            setStatus('timeout');
            return;
        }
        var m = Math.floor(expirySeconds / 60);
        var s = expirySeconds % 60;
        if (countdownEl) {
            countdownEl.textContent =
                String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }
    }, 1000);

    // ── Status helpers ──────────────────────────────────────────────────────
    function setStatus(state, message) {
        var dot       = document.getElementById('status-dot');
        var text      = document.getElementById('status-text');
        var badge     = document.getElementById('payment-badge');
        var badgeText = document.getElementById('badge-text');
        var overlay   = document.getElementById('paid-overlay');
        var banner    = document.getElementById('success-banner');

        if (dot)  dot.className  = 'status-dot ' + state;

        if (state === 'paid') {
            paymentDone = true;
            if (text)      text.textContent      = message || 'Payment confirmed!';
            if (badgeText) badgeText.textContent  = 'Payment Confirmed';
            if (badge)     badge.classList.add('paid');
            if (overlay)   overlay.style.display  = 'flex';
            if (banner)    banner.style.display   = 'block';

            clearInterval(pollInterval);
            clearInterval(countdownTimer);

            setTimeout(function () {
                window.location.href = successUrl + '?payment=success';
            }, 2500);

        } else if (state === 'timeout') {
            if (text)      text.textContent      = 'QR code expired. Payment was cancelled.';
            if (badgeText) badgeText.textContent  = 'QR Expired';
            clearInterval(pollInterval);
            cancelPendingPayment(false);

        } else if (state === 'cancelled') {
            if (text)      text.textContent      = message || 'Payment cancelled.';
            if (badgeText) badgeText.textContent  = 'Cancelled';
            clearInterval(pollInterval);
            clearInterval(countdownTimer);

        } else {
            if (text)      text.textContent      = message || 'Waiting for payment…';
            if (badgeText) badgeText.textContent  = 'Awaiting Payment';
        }
    }

    function cancelPendingPayment(redirect) {
        if (paymentDone) return;

        fetch(cancelUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     csrfToken,
            },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (redirect !== false) {
                window.location.href = data.redirect_url || successUrl;
            }
        })
        .catch(function () {
            if (redirect !== false) {
                window.location.href = successUrl;
            }
        });
    }

    var cancelBtn = document.getElementById('btn-cancel-payment');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            if (paymentDone) return;
            if (confirm('Cancel this payment? Your order will not be placed and stock will not be reserved.')) {
                setStatus('cancelled', 'Payment cancelled.');
                cancelPendingPayment(true);
            }
        });
    }

    var continueBtn = document.getElementById('btn-continue-shop');
    if (continueBtn) {
        continueBtn.addEventListener('click', function (e) {
            if (paymentDone) return;
            e.preventDefault();
            if (confirm('Leave without paying? Your order will be cancelled.')) {
                setStatus('cancelled', 'Payment cancelled.');
                cancelPendingPayment(true);
            }
        });
    }

    // ── Polling (every 4 s, max 75 times ≈ 5 min) ──────────────────────────
    var pollCount   = 0;
    var maxPolls    = 75;
    var pollTimerEl = document.getElementById('poll-timer');

    var pollInterval = setInterval(function () {
        pollCount++;

        if (pollCount > maxPolls) {
            clearInterval(pollInterval);
            setStatus('timeout');
            return;
        }

        if (pollTimerEl) pollTimerEl.textContent = '(check ' + pollCount + ')';

        fetch(pollUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.paid) {
                setStatus('paid', data.message);
            }
        })
        .catch(function () {
            // Silent — keep polling on network hiccups
        });

    }, 4000);

    // ── KHQR Debug Decoder ──────────────────────────────────────────────────
    // Parses the TLV structure of the KHQR string and validates its CRC16
    // checksum entirely client-side, so you can see exactly what's wrong
    // with a QR that scans but doesn't trigger a payment in the bank app.
    (function runKhqrDebug() {
        var debugOutput = document.getElementById('khqr-debug-output');
        var debugToggle  = document.getElementById('khqr-debug-toggle');
        var debugBody    = document.getElementById('khqr-debug-body');
        var pillEl       = document.getElementById('khqr-debug-status-pill');

        if (!debugOutput) return; // panel not present (APP_DEBUG off)

        if (debugToggle && debugBody) {
            debugToggle.addEventListener('click', function () {
                debugBody.style.display = debugBody.style.display === 'none' ? 'block' : 'none';
            });
        }

        function crc16ccitt(str) {
            var crc = 0xFFFF;
            for (var i = 0; i < str.length; i++) {
                crc ^= (str.charCodeAt(i) << 8);
                for (var b = 0; b < 8; b++) {
                    if (crc & 0x8000) {
                        crc = ((crc << 1) ^ 0x1021) & 0xFFFF;
                    } else {
                        crc = (crc << 1) & 0xFFFF;
                    }
                }
            }
            return crc.toString(16).toUpperCase().padStart(4, '0');
        }

        var CURRENCY_LABELS = { '840': 'USD', '116': 'KHR' };

        function parseTLV(str) {
            var nodes = [];
            var i = 0;
            while (i < str.length) {
                if (i + 4 > str.length) {
                    nodes.push({ error: 'Truncated tag at position ' + i });
                    break;
                }
                var tag = str.slice(i, i + 2);
                var len = parseInt(str.slice(i + 2, i + 4), 10);
                if (isNaN(len)) {
                    nodes.push({ tag: tag, error: 'Invalid length field at position ' + i });
                    break;
                }
                var value = str.slice(i + 4, i + 4 + len);
                if (value.length !== len) {
                    nodes.push({ tag: tag, length: len, value: value, error: 'Declared length ' + len + ' but only ' + value.length + ' chars remain' });
                    break;
                }
                nodes.push({ tag: tag, length: len, value: value });
                i += 4 + len;
            }
            return nodes;
        }

        function decodeKHQR(qrString) {
            var result = {
                length: qrString.length,
                errors: [],
                warnings: [],
                crc: { found: null, computed: null, match: false },
                summary: {}
            };

            qrString = qrString.trim();

            if (!qrString.startsWith('000201')) {
                result.errors.push('Does not start with "000201" (Payload Format Indicator). Found: "' + qrString.slice(0, 6) + '". The string may have been built manually and is missing/corrupting the leading tag.');
            }

            var crcTagIndex = qrString.lastIndexOf('6304');
            if (crcTagIndex === -1) {
                result.errors.push('No CRC tag "6304" found — the string is missing its checksum entirely.');
            } else {
                var expectedCrc = qrString.slice(crcTagIndex + 4, crcTagIndex + 8);
                var dataForCrc  = qrString.slice(0, crcTagIndex + 4);
                var computedCrc = crc16ccitt(dataForCrc);

                result.crc.found    = expectedCrc;
                result.crc.computed = computedCrc;
                result.crc.match    = expectedCrc.toUpperCase() === computedCrc.toUpperCase();

                if (!result.crc.match) {
                    result.errors.push('CRC mismatch: string contains "' + expectedCrc + '" but the computed CRC16/CCITT-FALSE is "' + computedCrc + '". Banking apps will silently reject a QR with a bad CRC — this is almost certainly why scanning does nothing.');
                }

                if (qrString.length !== crcTagIndex + 8) {
                    result.warnings.push('There are ' + (qrString.length - (crcTagIndex + 8)) + ' extra character(s) after the CRC value.');
                }
            }

            var parseEnd = crcTagIndex !== -1 ? crcTagIndex + 8 : qrString.length;
            var fields = parseTLV(qrString.slice(0, parseEnd));

            var byTag = {};
            fields.forEach(function (f) {
                if (f.error) result.errors.push('Field parse error (tag ' + (f.tag || '?') + '): ' + f.error);
                if (f.tag) byTag[f.tag] = f.value;
            });

            if (byTag['53']) result.summary.currency = CURRENCY_LABELS[byTag['53']] || ('Unknown (' + byTag['53'] + ')');
            if (byTag['54']) result.summary.amount = byTag['54'];
            if (byTag['58']) {
                result.summary.countryCode = byTag['58'];
                if (byTag['58'] !== 'KH') {
                    result.warnings.push('Country code is "' + byTag['58'] + '", expected "KH".');
                }
            }
            if (byTag['59']) result.summary.merchantName = byTag['59'];
            if (byTag['60']) result.summary.merchantCity = byTag['60'];

            ['29', '30', '15'].forEach(function (accTag) {
                if (byTag[accTag]) {
                    var sub = parseTLV(byTag[accTag]);
                    sub.forEach(function (s) {
                        if (s.value && s.value.indexOf('@') !== -1) {
                            result.summary.accountId = s.value;
                        }
                    });
                }
            });

            if (!result.summary.accountId) {
                result.warnings.push('No account ID containing "@" found in the merchant account template (tags 29/30/15). Confirm your account ID was actually embedded.');
            } else if (!/^[^@]+@[a-z]{2,10}$/i.test(result.summary.accountId)) {
                result.warnings.push('Account ID "' + result.summary.accountId + '" doesn\'t match the typical "username@bankcode" pattern. Confirm the bank suffix is a real registered Bakong participant code (e.g. @abaa, @aclb, @wing) — not a placeholder or sandbox value.');
            }

            return result;
        }

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function render(qrString) {
            var result = decodeKHQR(qrString);
            var hasErrors = result.errors.length > 0;
            var hasWarnings = result.warnings.length > 0;

            // Pill in the toggle bar
            if (pillEl) {
                if (hasErrors) {
                    pillEl.textContent = result.errors.length + ' issue(s) found';
                    pillEl.className = 'khqr-pill error';
                } else if (hasWarnings) {
                    pillEl.textContent = result.warnings.length + ' warning(s)';
                    pillEl.className = 'khqr-pill warn';
                } else {
                    pillEl.textContent = 'looks valid';
                    pillEl.className = 'khqr-pill ok';
                }
            }

            var html = '';

            html += '<div class="khqr-row"><span class="k">String length</span><span class="v">' + result.length + ' chars</span></div>';
            html += '<div class="khqr-row"><span class="k">CRC in string</span><span class="v">' + (result.crc.found || '—') + '</span></div>';
            html += '<div class="khqr-row"><span class="k">CRC computed</span><span class="v">' + (result.crc.computed || '—') + '</span></div>';
            html += '<div class="khqr-row"><span class="k">CRC match</span><span class="v">' + (result.crc.match ? '✅ yes' : '❌ no') + '</span></div>';

            if (result.summary.accountId) {
                html += '<div class="khqr-row"><span class="k">Account ID</span><span class="v">' + escapeHtml(result.summary.accountId) + '</span></div>';
            }
            if (result.summary.merchantName) {
                html += '<div class="khqr-row"><span class="k">Merchant name</span><span class="v">' + escapeHtml(result.summary.merchantName) + '</span></div>';
            }
            if (result.summary.merchantCity) {
                html += '<div class="khqr-row"><span class="k">Merchant city</span><span class="v">' + escapeHtml(result.summary.merchantCity) + '</span></div>';
            }
            if (result.summary.currency) {
                html += '<div class="khqr-row"><span class="k">Currency</span><span class="v">' + escapeHtml(result.summary.currency) + '</span></div>';
            }
            if (result.summary.amount) {
                html += '<div class="khqr-row"><span class="k">Amount field</span><span class="v">' + escapeHtml(result.summary.amount) + '</span></div>';
            }
            if (result.summary.countryCode) {
                html += '<div class="khqr-row"><span class="k">Country code</span><span class="v">' + escapeHtml(result.summary.countryCode) + '</span></div>';
            }

            if (hasErrors) {
                html += '<ul class="khqr-msg-list errors">';
                result.errors.forEach(function (e) { html += '<li>' + escapeHtml(e) + '</li>'; });
                html += '</ul>';
            }
            if (hasWarnings) {
                html += '<ul class="khqr-msg-list warnings">';
                result.warnings.forEach(function (w) { html += '<li>' + escapeHtml(w) + '</li>'; });
                html += '</ul>';
            }
            if (!hasErrors && !hasWarnings) {
                html += '<p style="color:#1a7a45;margin:8px 0 0;">No structural problems detected. If the bank app still does nothing, the account ID is most likely not registered as a real production Bakong participant — confirm it directly with your bank.</p>';
            }

            html += '<button type="button" class="khqr-raw-toggle" id="khqr-raw-toggle">Show raw QR string</button>';
            html += '<div class="khqr-raw-string" id="khqr-raw-string" style="display:none">' + escapeHtml(qrString) + '</div>';

            debugOutput.innerHTML = html;

            var rawToggle = document.getElementById('khqr-raw-toggle');
            var rawString = document.getElementById('khqr-raw-string');
            if (rawToggle && rawString) {
                rawToggle.addEventListener('click', function () {
                    rawString.style.display = rawString.style.display === 'none' ? 'block' : 'none';
                });
            }
        }

        if (qrInput && qrInput.value.trim()) {
            render(qrInput.value.trim());
        } else {
            debugOutput.innerHTML = '<p style="margin:0;">No QR string was generated for this order — nothing to decode. Check the "QR not available" message above for the reason.</p>';
            if (pillEl) {
                pillEl.textContent = 'no QR';
                pillEl.className = 'khqr-pill error';
            }
        }
    })();

})();
</script>
@endsection