
{{-- resources/views/layouts/partials/footer.blade.php --}}

<footer class="bg-white border-t border-gray-200 text-gray-700">

    {{-- Main Footer --}}
    <div class="py-12">
        <div class="max-w-[1280px] mx-auto px-4">

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Brand --}}
                <div class="col-span-2 lg:col-span-1">

                    <a href="{{ route('home') }}" class="inline-flex items-center mb-5">
                        <span class="text-2xl font-semibold">
                            PhoneBy<span class="text-blue-600 font-bold">Ashahi</span>
                        </span>
                    </a>

                    <p class="text-sm text-gray-600 leading-7 mb-6">
                        {{ __('messages.about_desc') }}
                    </p>

                    <h4 class="font-semibold mb-3">
                        {{ __('messages.subscribe_newsletter') }}
                    </h4>

                    <form action="#" method="POST" class="space-y-3">
                        @csrf

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email address"
                            required
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-600 focus:ring focus:ring-blue-200 outline-none">

                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg text-sm font-medium transition">
                            Subscribe
                        </button>
                    </form>

                </div>

                {{-- Quick Links --}}
                <div>

                    <h4 class="font-semibold text-lg mb-5">
                        Quick Links
                    </h4>

                    <ul class="space-y-3 text-sm">

                        <li>
                            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">
                            {{__('messages.home')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/') }}" class="hover:text-blue-600 transition">
                                {{__('messages.shop')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('about') }}" class="hover:text-blue-600 transition">
                                {{__('messages.about_us')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('contact.index') }}" class="hover:text-blue-600 transition">
                                {{__('messages.contact_us')}}
                            </a>
                        </li>

                      

                    </ul>

                </div>

                {{-- Customer Service --}}
                <div>

                    <h4 class="font-semibold text-lg mb-5">
                        {{__('messages.customer_services')}}
                    </h4>

                    <ul class="space-y-3 text-sm">

                     
                        <li>
                            <a href="{{ route('shipping-policy') }}" class="hover:text-blue-600 transition">
                                {{__('messages.shipping_policy')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('warranty-policy')}}" class="hover:text-blue-600 transition">
                                {{__('messages.warranty_policy')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('privacy-policy') }}"  class="hover:text-blue-600 transition">
                                {{__('messages.privacy_policy')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('terms-and-conditions') }}"  class="hover:text-blue-600 transition">
                                {{__('messages.terms_conditions')}}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('faq') }}"  class="hover:text-blue-600 transition">
                                {{__('messages.faq')}}
                            </a>
                        </li>

                    </ul>

                </div>

                {{-- Contact --}}
                <div>

                    <h4 class="font-semibold text-lg mb-5">
                       {{__('messages.contact_info')}}
                    </h4>

                    <ul class="space-y-4 text-sm">

                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-blue-600 mt-1"></i>
                            <span>
                                {{__('messages.locate')}}
                            </span>
                        </li>

                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-blue-600"></i>
                            <span>
                                +855 96 231 6406
                            </span>
                        </li>

                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-blue-600"></i>
                            <span>
                                support@phonebyashahi.com
                            </span>
                        </li>

                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            <span>
                                Monday - Sunday
                                <br>
                                8:00 AM - 9:00 PM
                            </span>
                        </li>

                    </ul>
                </div>


                   {{-- Social --}}
                    <div class="flex flex-wrap gap-3 mt-6">

                        <a href="#"
                           class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:scale-105 transition">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>

                        <a href="#"
                           class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center hover:scale-105 transition">
                            <i class="fa-brands fa-facebook-messenger"></i>
                        </a>

                        <a href="#"
                           class="w-10 h-10 rounded-full bg-cyan-500 text-white flex items-center justify-center hover:scale-105 transition">
                            <i class="fa-brands fa-telegram"></i>
                        </a>

                        <a href="#"
                           class="w-10 h-10 rounded-full bg-pink-600 text-white flex items-center justify-center hover:scale-105 transition">
                            <i class="fa-brands fa-instagram"></i>
                        </a>

                        <a href="#"
                           class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:scale-105 transition">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>

                        <a href="#"
                           class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center hover:scale-105 transition">
                            <i class="fa-brands fa-youtube"></i>
                        </a>

                    </div>


            </div>

        </div>
    </div>

    {{-- Bottom --}}
    <div class="border-t border-gray-200">

        <div class="max-w-7xl mx-auto px-4 py-6">

            <div class="flex flex-col lg:flex-row items-center justify-between gap-5">

                <p class="text-sm text-gray-500 text-center lg:text-left">
                    © {{ date('Y') }}
                    <strong class="text-gray-800">
                        PhoneByAshahi
                    </strong>.
                    All Rights Reserved. Designed & Developed by Chhayna_Dev.
                </p>

                {{-- Payment Methods --}}
                <div class="flex flex-wrap items-center justify-center gap-3 text-gray-500 text-2xl">

                    <span class="text-sm font-medium ml-2">
                        ABA • Bakong
                    </span>

                </div>

            </div>

        </div>

    </div>

</footer>

