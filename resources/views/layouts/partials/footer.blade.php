{{-- resources/views/layouts/partials/footer.blade.php --}}

<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">

                {{-- Brand / Subscribe column --}}
                <div class="footer-col footer-brand">
                    <a href="{{ route('home') }}" class="footer-logo">
                        <span class="logo-text">PhoneBy<strong>Ashahi</strong></span>
                    </a>
                    <h4 class="footer-heading">Subscribe More Info</h4>
                    <form action="#" method="POST" class="footer-subscribe">
                        @csrf
                        <input type="email" name="email" placeholder="Enter your email" class="subscribe-input" required>
                        <button type="submit" class="subscribe-btn">Subscribe</button>
                    </form>
                </div>

                {{-- Information column --}}
                <div class="footer-col">
                    <h4 class="footer-heading">Information</h4>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">More Search</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>

                {{-- Helpful Links column --}}
                <div class="footer-col">
                    <h4 class="footer-heading">Helpful Links</h4>
                    <ul class="footer-links">
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Supports</a></li>
                        <li><a href="#">Terms & Condition</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>

                {{-- Contact Us column --}}
                <div class="footer-col">
                    <h4 class="footer-heading">Contact Us</h4>
                    <ul class="footer-links footer-contact">
                        <li>+855 962316406</li>
                        <li>asahi@gmail.com</li>
                    </ul>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Messenger"><i class="fa-brands fa-facebook-messenger"></i></a>
                        <a href="#" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
                        <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="footer-bottom">
        <div class="container">
            <p class="copyright">
                &copy; {{ date('Y') }} <strong>Ashahi Shop</strong>. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background: var(--clr-white);
        color: var(--clr-dark);
        border-top: 1px solid var(--clr-border);
    }

    /* Main section */
    .footer-main {
        padding: 48px 0 36px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1fr;
        gap: 40px;
    }

    /* Brand col */
    .footer-logo {
        display: inline-flex;
        align-items: center;
        margin-bottom: 18px;
    }

    .footer-logo-img {
        height: 56px;
        width: auto;
        border-radius: 50%;
    }

    .footer-subscribe {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
        max-width: 300px;
    }

    .subscribe-input {
        padding: 11px 16px;
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-md);
        background: var(--clr-surface);
        font-size: 13.5px;
        color: var(--clr-dark);
        outline: none;
    }

    .subscribe-input::placeholder {
        color: var(--clr-muted);
    }

    .subscribe-btn {
        align-self: flex-start;
        padding: 10px 22px;
        background: #0056b3;
        color: #ffffff;
        border: none;
        border-radius: var(--radius-md);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background var(--ease);
    }

    .subscribe-btn:hover {
        background: #0b3b6d;
    }

    /* Link columns */
    .footer-heading {
        font-size: 16px;
        font-weight: 700;
        color: var(--clr-dark);
        margin-bottom: 16px;
    }

    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .footer-links a {
        font-size: 14px;
        color: var(--clr-body);
        transition: color var(--ease), padding-left var(--ease);
    }

    .footer-links a:hover {
        color: var(--clr-primary);
        padding-left: 4px;
    }

    .footer-contact li {
        font-size: 14px;
        color: var(--clr-body);
    }

    /* Social icons */
    .footer-social {
        display: flex;
        gap: 10px;
        margin-top: 14px;
    }

    .footer-social a {
        width: 30px;
        height: 30px;
        background: var(--clr-dark);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 13px;
        color: #fff;
        transition: all var(--ease);
    }

    .footer-social a[aria-label="Facebook"] { background: #1877F2; }
    .footer-social a[aria-label="Messenger"] { background: #00B2FF; }
    .footer-social a[aria-label="Telegram"] { background: #229ED9; }
    .footer-social a[aria-label="TikTok"] { background: #000; }

    .footer-social a:hover {
        opacity: 0.85;
        transform: translateY(-2px);
    }

    /* Bottom bar */
    .footer-bottom {
        padding: 16px 0;
        border-top: 1px solid var(--clr-border);
    }

    .footer-bottom .container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .copyright {
        font-size: 12.5px;
        color: var(--clr-muted);
    }

    /* Responsive */
    @media (max-width: 900px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
    }

    @media (max-width: 600px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }
    }
</style>