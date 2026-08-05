

@php
    $phone     = $phone     ?? '+855967177746';
    $whatsapp  = $whatsapp  ?? '855967177746';
    $telegram  = $telegram  ?? '@Norn_Chhayna';
    $messenger = $messenger ?? 'yourpage';
@endphp 

<style>
    .fcw-widget {
        position: fixed;
        right: 20px;
        bottom: 30px;
        z-index: 9999;
        display: flex;
        flex-direction: column-reverse;
        align-items: flex-end;
        gap: 14px;
    }

    .fcw-main {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #006098;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        border: none;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(0,0,0,0.25);
        position: relative;
        z-index: 2;
        transition: transform 0.4s ease;
    }
    .fcw-main:active { transform: scale(0.94); }
    .fcw-main svg { width: 22px; height: 22px; transition: opacity 0.15s ease, transform 0.2s ease; }

    .fcw-main::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: #0c4fa6;
        opacity: 0.5;
        animation: fcwPulse 2.2s ease-out infinite;
        z-index: -1;
    }
    .fcw-widget.fcw-open .fcw-main::after { animation: none; opacity: 0; }

    @keyframes fcwPulse {
        0%   { transform: scale(1);   opacity: 0.5; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    /* icon-swap: chat icon <-> close (x) icon */
    .fcw-icon-close { position: absolute; opacity: 0; transform: rotate(-45deg) scale(0.6); }
    .fcw-widget.fcw-open .fcw-icon-chat  { opacity: 0; transform: rotate(45deg) scale(0.6); }
    .fcw-widget.fcw-open .fcw-icon-close { opacity: 1; transform: rotate(0) scale(1); }

    .fcw-items {
        display: flex;
        flex-direction: column-reverse;
        align-items: flex-end;
        gap: 12px;
    }

    .fcw-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        opacity: 0;
        transform: translateY(16px) scale(0.8);
        pointer-events: none;
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .fcw-widget.fcw-open .fcw-item {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* stagger so the bubble closest to the main button (phone) opens first,
       then each one further up follows a beat later */
    .fcw-widget.fcw-open .fcw-item:nth-child(1) { transition-delay: 0s; }
    .fcw-widget.fcw-open .fcw-item:nth-child(2) { transition-delay: 0.06s; }
    .fcw-widget.fcw-open .fcw-item:nth-child(3) { transition-delay: 0.12s; }
    .fcw-widget.fcw-open .fcw-item:nth-child(4) { transition-delay: 0.18s; }

    .fcw-label {
        background: #1e293b;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 5px;
        white-space: nowrap;
        opacity: 0;
        transform: translateX(6px);
        transition: opacity 0.18s ease, transform 0.18s ease;
        pointer-events: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .fcw-item:hover .fcw-label {
        opacity: 1;
        transform: translateX(0);
    }

    .fcw-bubble {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
        flex-shrink: 0;
    }
    .fcw-item:hover .fcw-bubble {
        transform: scale(1.08);
        box-shadow: 0 5px 16px rgba(0,0,0,0.28);
    }
    .fcw-bubble svg { width: 22px; height: 22px; }

    .fcw-phone     { background: #2563eb; }
    .fcw-whatsapp  { background: #25D366; }
    .fcw-messenger { background: linear-gradient(135deg,#00B2FF,#006AFF); }
    .fcw-telegram  { background: #229ED9; }

    @media (max-width: 480px) {
        .fcw-widget { right: 12px; bottom: 16px; }
        .fcw-main { width: 50px; height: 50px; }
        .fcw-bubble { width: 42px; height: 42px; }
        .fcw-label { display: none; }
    }
</style>

<div class="fcw-widget" id="fcwWidget">

    <button type="button" class="fcw-main" id="fcwToggle" aria-label="Open contact options" aria-expanded="false">
        <svg class="fcw-icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
        </svg>
        <svg class="fcw-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
    </button>

    <div class="fcw-items">

        <div class="fcw-item">
            <span class="fcw-label">Call us</span>
            <a href="tel:{{ $phone }}" class="fcw-bubble fcw-phone" aria-label="Phone">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1.1.4 2.2.7 3.2a2 2 0 0 1-.5 2.1L8 10.3a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c1 .4 2.1.6 3.2.8a2 2 0 0 1 1.7 2Z"/>
                </svg>
            </a>
        </div>

        <!-- <div class="fcw-item">
            <span class="fcw-label">WhatsApp</span>
            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="fcw-bubble fcw-whatsapp" aria-label="WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.5-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5C10 9.5 9.4 8 9.1 7.4c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.3.3-1 1-1 2.4s1 2.8 1.2 3c.1.2 2 3 4.8 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.5-.3ZM12 2C6.5 2 2 6.4 2 12c0 1.9.5 3.6 1.4 5.1L2 22l5-1.3c1.4.8 3.1 1.3 4.9 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2Zm0 18.1c-1.6 0-3.1-.4-4.5-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.1 8.1 0 0 1 3.9 12c0-4.5 3.6-8.1 8.1-8.1s8.1 3.6 8.1 8.1-3.6 8.1-8.1 8.1Z"/></svg>
            </a>
        </div> -->

        <div class="fcw-item">
            <span class="fcw-label">Messenger</span>
            <a href="https://m.me/{{ $messenger }}" target="_blank" rel="noopener" class="fcw-bubble fcw-messenger" aria-label="Messenger">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.5 2 2 6.1 2 11.4c0 3 1.5 5.6 3.8 7.4V22l3.5-1.9c.9.3 1.9.4 2.9.4 5.5 0 10-4.1 10-9.4S17.5 2 12 2Zm1 12.7-2.6-2.8-5 2.8 5.5-5.9 2.6 2.8 5-2.8-5.5 5.9Z"/></svg>
            </a>
        </div>
<x-chatbot-widget
    whatsapp="85517935151"
    shop-name="Asahi Phone Shop"
    products-url="/products"
/>
        <div class="fcw-item">
            <span class="fcw-label">Telegram</span>
            <a href="https://t.me/{{ $telegram }}" target="_blank" rel="noopener" class="fcw-bubble fcw-telegram" aria-label="Telegram">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3 2.7 11.6c-1.2.5-1.2 1.2-.2 1.5l4.9 1.5 1.9 5.8c.2.6.4.8.9.8.4 0 .6-.2.9-.5l2.2-2.1 4.6 3.4c.8.5 1.4.2 1.6-.8L23.9 5.5c.3-1.3-.5-1.9-1.9-1.2Zm-3.6 3.4-8 7.2-.3 3.2-1.6-4.9 9.1-5.7c.4-.3.8-.1.5.2Z"/></svg>
            </a>
        </div>

    </div>
</div>

<script>
(function () {
    var widget = document.getElementById('fcwWidget');
    var toggle = document.getElementById('fcwToggle');

    toggle.addEventListener('click', function () {
        var isOpen = widget.classList.toggle('fcw-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        toggle.setAttribute('aria-label', isOpen ? 'Close contact options' : 'Open contact options');
    });

    document.addEventListener('click', function (e) {
        if (!widget.contains(e.target)) {
            widget.classList.remove('fcw-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open contact options');
        }
    });
})();
</script>