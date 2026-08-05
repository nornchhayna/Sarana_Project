@extends('layouts.app')

@section('content')

<div class="max-w-[1280px] mx-auto px-6 py-6">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 bg-white rounded-2xl  overflow-hidden border border-gray-200">

     <div class="h-[500px]">
    <iframe
        class="w-full h-full border-0 rounded-lg"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4727.678090740238!2d104.8007837!3d11.598405999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31094dd48f8cd5c7%3A0xa336c9b080eed3d3!2sNational%20Polytechnic%20Institute%20of%20Cambodia%20(NPIC)!5e1!3m2!1sen!2skh!4v1784794875957!5m2!1sen!2skh"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>

        {{-- Contact Form --}}
        <div class="p-6 lg:p-10">

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800">
                    Contact Us
                </h2>

                <p class="mt-2 text-gray-500">
                    Our team would love to hear from you
                </p>
            </div>


            @if(session('success'))
                <div class="mb-5 rounded-lg bg-green-100 px-4 py-3 text-green-700 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif


            <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">

                @csrf


                {{-- Name + Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Full Name
                        </label>

                        <input 
                            type="text"
                            name="name"
                            value="{{ old('name', auth()->user()->name ?? '') }}"
                            placeholder="Enter your full name"
                            required
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none"
                        >

                        @error('name')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>


                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Email Address
                        </label>

                        <input 
                            type="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email ?? '') }}"
                            placeholder="example@domain.com"
                            required
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none"
                        >

                        @error('email')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                </div>



                {{-- Phone + Subject --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Phone Number (Optional)
                        </label>

                        <input 
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="e.g. +1 234 567 890"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none"
                        >
                    </div>


                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Subject
                        </label>

                        <input 
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            placeholder="What is this regarding?"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none"
                        >
                    </div>

                </div>



                {{-- Message --}}
                <div>

                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Message
                    </label>


                    <textarea
                        name="message"
                        rows="5"
                        placeholder="Type your message details here..."
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none"
                    >{{ old('message') }}</textarea>


                    @error('message')
                        <small class="text-red-500">{{ $message }}</small>
                    @enderror

                </div>



                {{-- Button --}}
                <div>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-8 py-3 font-semibold text-white hover:bg-blue-700 transition duration-200"
                    >
                        Send message
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection