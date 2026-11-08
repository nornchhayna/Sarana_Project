@php
    $telegram    = $telegram    ?? '@Norn_Chhayna';
    $shopName    = $shopName    ?? 'Asahi Phone Shop';
    $productsUrl = $productsUrl ?? '/products';
    $ordersUrl   = $ordersUrl   ?? '/orders';
    $orders      = $orders      ?? [];

    // Normalize orders (works whether it's an Eloquent collection, plain array, or stdClass)
    $normalizedOrders = collect($orders)->map(function ($order) {
        if (is_object($order) && method_exists($order, 'toArray')) {
            $order = $order->toArray();
        } else {
            $order = (array) $order;
        }

        // Format the date nicely instead of leaving a raw ISO timestamp.
        $rawDate = $order['date'] ?? ($order['created_at'] ?? null);
        $formattedDate = '';
        if ($rawDate) {
            try {
                $formattedDate = \Illuminate\Support\Carbon::parse($rawDate)->format('M j, Y g:i A');
            } catch (\Exception $e) {
                $formattedDate = $rawDate;
            }
        }

        return [
            'id'           => $order['id'] ?? '',
            'order_number' => $order['order_number'] ?? ($order['id'] ?? ''),
            'status'       => $order['status'] ?? 'processing',
            'total'        => $order['total'] ?? null,
            'date'         => $formattedDate,
        ];
    })->values();
@endphp

<style>
    .cbw-launcher {
        position: fixed;
        right: 20px;
        bottom: 30px;
        z-index: 9999;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #2182b6;
        border: none;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(0,0,0,0.25);
    }
    .cbw-launcher svg { width: 26px; height: 26px; }
    .cbw-launcher .cbw-icon-close { position: absolute; opacity: 0; }
    .cbw-launcher.cbw-active .cbw-icon-chat  { opacity: 0; }
    .cbw-launcher.cbw-active .cbw-icon-close { opacity: 1; }

    .cbw-panel {
        position: fixed;
        right: 20px;
        bottom: 100px;
        z-index: 9999;
        width: 340px;
        max-width: calc(100vw - 40px);
        max-height: 540px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .cbw-panel.cbw-open {
        display: flex;
        opacity: 1;
        transform: translateY(0);
    }

    .cbw-header {
        background: #2563eb;
        color: #fff;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cbw-header .cbw-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 600;
    }
    .cbw-header .cbw-title { font-size: 14px; font-weight: 600; margin: 0; }
    .cbw-header .cbw-subtitle { font-size: 11px; opacity: 0.85; margin: 0; }

    .cbw-body {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8fafc;
    }

    .cbw-msg {
        max-width: 85%;
        font-size: 13.5px;
        line-height: 1.45;
        padding: 8px 12px;
        border-radius: 12px;
    }
    .cbw-msg-bot {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 4px;
        align-self: flex-start;
        color: #1e293b;
    }
    .cbw-msg-user {
        background: #2563eb;
        color: #fff;
        border-bottom-right-radius: 4px;
        align-self: flex-end;
    }

    .cbw-options {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-self: flex-start;
        max-width: 100%;
    }
    .cbw-option-btn {
        background: #fff;
        border: 1px solid #2563eb;
        color: #2563eb;
        font-size: 12.5px;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 16px;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .cbw-option-btn:hover { background: #2563eb; color: #fff; }

    .cbw-footer {
        padding: 8px 12px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }
    .cbw-footer button {
        background: none;
        border: none;
        color: #64748b;
        font-size: 11.5px;
        cursor: pointer;
    }
    .cbw-footer button:hover { color: #2563eb; }

    @media (max-width: 480px) {
        .cbw-launcher { right: 12px; bottom: 75px; width: 50px; height: 50px; }
        .cbw-panel { right: 12px; bottom: 130px; width: calc(100vw - 24px); }
    }
</style>

<button type="button" class="cbw-launcher" id="cbwLauncher" aria-label="Open chat">
    <svg class="cbw-icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
    </svg>
    <svg class="cbw-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 6 6 18M6 6l12 12"/>
    </svg>
</button>

<div class="cbw-panel" id="cbwPanel">
    <div class="cbw-header">
        <div class="cbw-avatar">{{ substr($shopName, 0, 1) }}</div>
        <div>
            <p class="cbw-title">{{ $shopName }}</p>
            <p class="cbw-subtitle">Usually replies in a few minutes</p>
        </div>
    </div>
    <div class="cbw-body" id="cbwBody"></div>
    <div class="cbw-footer">
        <button type="button" id="cbwRestart">Start over</button>
    </div>
</div>

<script>
(function () {
    var telegramUsername = "{{ ltrim($telegram, '@') }}";
    var productsUrl = "{{ $productsUrl }}";
    var ordersUrl   = "{{ $ordersUrl }}";
    var userOrders  = {!! json_encode($normalizedOrders) !!};

    // ---- Edit this tree to change what the bot says / offers ----
    var botTree = {
        root: {
            text: "Hi! I'm the " + "{{ $shopName }}" + " assistant. What can I help you with?",
            options: [
                { label: "Shop by brand",         next: "shopBrand" },
                { label: "Shop by category",      next: "shopCategory" },
                { label: "Shop by budget",        next: "shopBudget" },
                { label: "Discounted products",   url: productsUrl + "?discounted=1" },
                { label: "Track my order",        next: "trackOrder" },
                { label: "Shipping & delivery",   next: "shipping" },
                { label: "Warranty info",         next: "warranty" },
                { label: "Payment methods",       next: "payment" },
                { label: "Trade-in / installment", next: "tradeIn" },
                { label: "Return & exchange",     next: "returns" },
                { label: "Store hours & branches", next: "hours" },
                { label: "Talk to a person",      action: "telegram" }
            ]
        },

        shopBrand: {
            text: "Which brand are you interested in?",
            options: [
                { label: "Samsung",  url: productsUrl + "?brand_id=50" },
                { label: "iPhone",   url: productsUrl + "?brand=apple" },
                { label: "Huawei",   url: productsUrl + "?brand=huawei" },
                { label: "Xiaomi",   url: productsUrl + "?brand=xiaomi" },
                { label: "Oppo",     url: productsUrl + "?brand=oppo" },
                { label: "Vivo",     url: productsUrl + "?brand=vivo" },
                { label: "Realme",   url: productsUrl + "?brand=realme" },
                { label: "Tecno",    url: productsUrl + "?brand=tecno" },
                { label: "\u2190 Back to menu", next: "root" }
            ]
        },

        shopCategory: {
            text: "What type of product are you looking for?",
            options: [
                { label: "Smartphones",       url: productsUrl + "?category=phones" },
                { label: "Tablets",           url: productsUrl + "?category=tablets" },
                { label: "Smart watches",     url: productsUrl + "?category=watches" },
                { label: "Earbuds & audio",   url: productsUrl + "?category=audio" },
                { label: "Chargers & cables", url: productsUrl + "?category=accessories" },
                { label: "Phone cases",       url: productsUrl + "?category=cases" },
                { label: "Power banks",       url: productsUrl + "?category=powerbanks" },
                { label: "\u2190 Back to menu", next: "root" }
            ]
        },

        shopBudget: {
            text: "What's your budget range?",
            options: [
                { label: "Under $150",   url: productsUrl + "?max_price=150" },
                { label: "$150 - $300",  url: productsUrl + "?min_price=150&max_price=300" },
                { label: "$300 - $600",  url: productsUrl + "?min_price=300&max_price=600" },
                { label: "$600 - $1000", url: productsUrl + "?min_price=600&max_price=1000" },
                { label: "$1000+",       url: productsUrl + "?min_price=1000" },
                { label: "\u2190 Back to menu", next: "root" }
            ]
        },

        // Options are built dynamically at runtime — see buildTrackOrderNode() below.
        trackOrder: {
            dynamic: true
        },

        shipping: {
            text: "We deliver across Phnom Penh within 1-2 days, and to other provinces in 2-4 days. Delivery fees depend on your location and are calculated at checkout. Same-day delivery is available for orders placed before 2 PM in Phnom Penh.",
            options: [
                { label: "\u2190 Back to menu", next: "root" },
                { label: "Talk to a person", action: "telegram" }
            ]
        },

        warranty: {
            text: "All phones come with a 1-year official warranty covering manufacturing defects. Accessories carry a 3-month warranty. Bring your receipt and the device to any of our branches for warranty service.",
            options: [
                { label: "\u2190 Back to menu", next: "root" },
                { label: "Talk to a person", action: "telegram" }
            ]
        },

        payment: {
            text: "We accept cash on delivery, bank transfer (ABA, ACLEDA, Wing), and credit/debit card payment at checkout. Installment plans are also available on selected phones.",
            options: [
                { label: "Installment plan details", next: "tradeIn" },
                { label: "\u2190 Back to menu", next: "root" }
            ]
        },

        tradeIn: {
            text: "Trade in your old phone for credit toward a new one, or split payments over 3-12 months through our partner installment providers. Bring your ID and current device to any branch to get a quote.",
            options: [
                { label: "\u2190 Back to menu", next: "root" },
                { label: "Talk to a person", action: "telegram" }
            ]
        },

        returns: {
            text: "Unopened items can be returned within 7 days of purchase with the original receipt. Devices with manufacturing defects can be exchanged within 30 days. Used or damaged items outside these terms aren't eligible for return.",
            options: [
                { label: "\u2190 Back to menu", next: "root" },
                { label: "Talk to a person", action: "telegram" }
            ]
        },

        hours: {
            text: "We're open daily 8:00 AM - 8:00 PM across our branches in Phnom Penh: Steung Meanchey, Kirirom, Phsar Depo, and Street 310. You can reach us anytime on Telegram at @" + "{{ ltrim($telegram, '@') }}" + ".",
            options: [
                {
                    label: "View branch locations",
                    url: "https://www.google.com/maps/place/National+Polytechnic+Institute+of+Cambodia+(NPIC)/@11.5984112,104.7982034,1086m/data=!3m2!1e3!4b1!4m6!3m5!1s0x31094dd48f8cd5c7:0xa336c9b080eed3d3!8m2!3d11.598406!4d104.8007837!16s%2Fg%2F1tdjtgv1?entry=ttu&g_ep=EgoyMDI2MDcxNS4wIKXMDSoASAFQAw%3D%3D"
                },
                { label: "\u2190 Back to menu", next: "root" },
                { label: "Talk to a person", action: "telegram" }
            ]
        }
    };

    var launcher = document.getElementById('cbwLauncher');
    var panel    = document.getElementById('cbwPanel');
    var body     = document.getElementById('cbwBody');
    var restart  = document.getElementById('cbwRestart');

    function addBotMessage(text) {
        var el = document.createElement('div');
        el.className = 'cbw-msg cbw-msg-bot';
        el.textContent = text;
        body.appendChild(el);
    }

    function addUserMessage(text) {
        var el = document.createElement('div');
        el.className = 'cbw-msg cbw-msg-user';
        el.textContent = text;
        body.appendChild(el);
    }

    function addOptions(options) {
        var wrap = document.createElement('div');
        wrap.className = 'cbw-options';
        options.forEach(function (opt) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cbw-option-btn';
            btn.textContent = opt.label;
            btn.addEventListener('click', function () {
                handleChoice(opt);
            });
            wrap.appendChild(btn);
        });
        body.appendChild(wrap);
        body.scrollTop = body.scrollHeight;
    }

    // Builds the "Track my order" node at runtime from userOrders.
    function buildTrackOrderNode() {
        if (!userOrders || userOrders.length === 0) {
            return {
                text: "We couldn't find any orders on your account yet. If you have an order number, tap below and we'll message our team on Telegram with it for you.",
                options: [
                    { label: "Go to My Orders", url: ordersUrl },
                    { label: "Enter order number", action: "askOrderNumber" },
                    { label: "\u2190 Back to menu", next: "root" }
                ]
            };
        }
        var opts = userOrders.map(function (order) {
            var priceText = order.total ? " - $" + order.total : "";
            return {
                label: "Order #" + order.order_number + priceText + " (" + order.status + ")",
                action: "telegramOrder",
                order: order
            };
        });
        opts.push({ label: "Go to My Orders", url: ordersUrl });
        opts.push({ label: "\u2190 Back to menu", next: "root" });
        return {
            text: "Here are your recent orders. Tap one to message us about it on Telegram:",
            options: opts
        };
    }

    function showNode(key) {
        var node = botTree[key];
        if (!node) return;
        if (node.dynamic) {
            node = buildTrackOrderNode();
        }
        addBotMessage(node.text);
        addOptions(node.options);
        body.scrollTop = body.scrollHeight;
    }

    function handleChoice(opt) {
        addUserMessage(opt.label);
        // remove the option buttons that were just clicked, so the chat reads top-to-bottom
        var allOptionRows = body.querySelectorAll('.cbw-options');
        var lastRow = allOptionRows[allOptionRows.length - 1];
        if (lastRow) lastRow.remove();

        if (opt.action === 'telegram') {
            window.open('https://t.me/' + telegramUsername, '_blank');
            addBotMessage("Opening Telegram for you...");
            addOptions([{ label: "\u2190 Back to menu", next: "root" }]);
            return;
        }

        if (opt.action === 'telegramOrder') {
            var order = opt.order;
            var msg = "សួស្តី Asahi Phone Shop ខ្ញុំបានការបញ្ជាទិញអីវ៉ាន់លេខរៀង #" + order.order_number + ".";
            if (order.date)   msg += " នៅថ្ងៃ " + order.date + ".";
            if (order.total)  msg += " តម្លៃសរុប: $" + order.total + ".";
            if (order.status) msg += " ស្ថានភាពបច្ចុប្បន្ន: " + order.status + " សូមក្រុមការងារត្រួតពិនិត្យមើល។";
            window.open('https://t.me/' + telegramUsername + '?text=' + encodeURIComponent(msg), '_blank');
            addBotMessage("Opening Telegram with your order details filled in...");
            addOptions([{ label: "\u2190 Back to menu", next: "root" }]);
            return;
        }

        if (opt.action === 'askOrderNumber') {
            window.open('https://t.me/' + telegramUsername + '?text=' + encodeURIComponent("Hi, I'd like to check the status of my order #"), '_blank');
            addBotMessage("Opening Telegram \u2014 just type in your order number and send.");
            addOptions([{ label: "\u2190 Back to menu", next: "root" }]);
            return;
        }

        if (opt.url) {
            window.open(opt.url, '_blank');
            return;
        }

        if (opt.next) {
            setTimeout(function () { showNode(opt.next); }, 300);
        }
    }

    function resetChat() {
        body.innerHTML = '';
        showNode('root');
    }

    launcher.addEventListener('click', function () {
        var isOpen = panel.classList.toggle('cbw-open');
        launcher.classList.toggle('cbw-active', isOpen);
        if (isOpen && body.children.length === 0) {
            resetChat();
        }
    });

    restart.addEventListener('click', resetChat);
})();
</script>