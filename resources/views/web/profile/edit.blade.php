@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 md:py-12 px-4">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-[1.7fr_1fr] gap-6 items-start">

        {{-- PROFILE INFO --}}
        <div class="bg-white rounded-2xl md:rounded-3xl overflow-hidden shadow-sm border border-gray-100">

            <div class="h-20 sm:h-24 bg-[#1562c7]"></div>

            <div class="px-5 sm:px-8 -mt-10 sm:-mt-12 relative">
                <div class="flex items-center gap-4 sm:gap-5">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('default-avatar.png') }}"
                         class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover ring-4 ring-white shadow-lg flex-shrink-0"
                         alt="{{ $user->name }}">
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ $user->name }}</h1>
                        <p class="text-gray-500 text-sm mt-0.5 truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="px-5 sm:px-8 pt-6 sm:pt-8 pb-8">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Your First Name" required
                               class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Your Phone Number"
                               class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
                        <select name="gender"
                                class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition appearance-none bg-no-repeat"
                                style="background-image:url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%228%22 viewBox=%220 0 12 8%22><path d=%22M1 1l5 5 5-5%22 stroke=%22%236b7280%22 stroke-width=%221.5%22 fill=%22none%22 fill-rule=%22evenodd%22/></svg>'); background-position:right 16px center;">
                            <option value="" disabled {{ !$user->gender ? 'selected' : '' }}>Select...</option>
                            <option value="male" @selected($user->gender == 'male')>Male</option>
                            <option value="female" @selected($user->gender == 'female')>Female</option>
                            <option value="other" @selected($user->gender == 'other')>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                               class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Avatar</label>
                        <label for="avatarInput"
                               class="flex items-center justify-between gap-3 bg-gray-50 hover:bg-gray-100 border border-dashed border-gray-300 rounded-xl px-4 py-3 cursor-pointer transition">
                            <span id="avatarFileName" class="text-sm text-gray-500 truncate">Choose a photo…</span>
                            <span class="flex-shrink-0 bg-indigo-50 text-indigo-600 font-semibold text-xs px-4 py-2 rounded-lg">Browse</span>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" class="hidden"
                               onchange="document.getElementById('avatarFileName').textContent = this.files[0] ? this.files[0].name : 'Choose a photo…'">
                    </div>
                </div>

                {{-- ADDRESS — reused to pre-fill the checkout form --}}
                <h2 class="text-sm font-bold text-gray-900 mt-8 mb-4">Shipping Address</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Street address"
                               class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="City"
                               class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Province</label>
                        <input type="text" name="province" value="{{ old('province', $user->province) }}" placeholder="Province"
                               class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 mt-8">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-7 py-3 rounded-xl transition-colors">
                        Update Profile
                    </button>
                    @if($user->avatar)
                        <a href="{{ route('profile.avatar.remove') }}"
                           class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-sm px-6 py-3 rounded-xl transition-colors">
                            Remove Avatar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- PASSWORD CHANGE --}}
        <div class="bg-white rounded-2xl md:rounded-3xl overflow-hidden shadow-sm border border-gray-100">
            <div class="px-6 sm:px-7 pt-7">
                <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-1">Change Password</h2>
                <p class="text-sm text-gray-500 mb-6">Use a strong password you don't use elsewhere.</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="px-6 sm:px-7 pb-7 flex flex-col gap-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" placeholder="••••••••" required
                           class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required
                           class="w-full bg-gray-50 border border-transparent focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none transition">
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-7 py-3 rounded-xl transition-colors mt-1">
                    Update Password
                </button>
            </form>
        </div>

    </div>
</div>
@endsection