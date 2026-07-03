<a href="{{ route('cart.index') }}" class="nav-link position-relative">
    <i class="fa-solid fa-cart-shopping"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
        {{ $cartCount }}
    </span>
</a>