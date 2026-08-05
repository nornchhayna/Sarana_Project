@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 md:py-12 px-4">
    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-2xl md:rounded-3xl overflow-hidden shadow-sm border border-gray-100">

            <!-- Banner -->
            <div class="h-32 sm:h-44 md:h-56 bg-[#1562c7] relative">
                <a href="{{ route('profile.edit') }}"
                   class="absolute top-4 right-4 sm:top-6 sm:right-6 inline-flex items-center gap-2 bg-white/95 hover:bg-white text-indigo-700 font-semibold px-4 py-2 sm:px-6 sm:py-3 rounded-xl sm:rounded-2xl shadow-md text-sm sm:text-base transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="hidden xs:inline">Edit Profile</span>
                </a>
            </div>

            <!-- Avatar + Name -->
            <div class="px-5 sm:px-8 -mt-16 sm:-mt-20 relative">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                    <img
                        src="{{ $user->avatar_url }}"
                        class="w-28 h-28 sm:w-36 sm:h-36 md:w-40 md:h-40 rounded-2xl sm:rounded-3xl object-cover ring-4 ring-white shadow-xl flex-shrink-0"
                        alt="{{ $user->name }}"
                    >
                    <div class="pb-2 sm:pb-6 min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $user->name }}</h1>
                        <p class="text-gray-500 text-base sm:text-lg mt-1 truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="px-5 sm:px-8 pt-8 sm:pt-10 pb-10 sm:pb-12">
                <h2 class="text-lg font-semibold text-gray-900 mb-5">Personal Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">

                    <!-- Phone -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Phone Number</p>
                        <div class="bg-gray-50 rounded-2xl p-5 flex items-center justify-between gap-3">
                            <span class="text-gray-800 font-medium truncate">{{ $user->hasPhone() ? $user->phone : 'Not set' }}</span>
                            @if($user->hasPhone())
                                <span class="flex-shrink-0 text-xs font-semibold px-3 py-1.5 rounded-full
                                    {{ $user->isPhoneVerified() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $user->isPhoneVerified() ? 'Verified' : 'Unverified' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Gender</p>
                        <div class="bg-gray-50 rounded-2xl p-5 text-gray-800 font-medium capitalize">
                            {{ $user->gender ?? 'Not set' }}
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Date of Birth</p>
                        <div class="bg-gray-50 rounded-2xl p-5 text-gray-800 font-medium">
                            {{ $user->date_of_birth?->format('d M, Y') ?? 'Not set' }}
                            @if($user->age)
                                <span class="text-gray-400 font-normal">({{ $user->age }} years old)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Address</p>
                        <div class="bg-gray-50 rounded-2xl p-5 text-gray-800 font-medium leading-relaxed">
                            {{ $user->full_address ?: 'Not set' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Section -->
            <div class="border-t border-gray-100 bg-gray-50/60 px-5 sm:px-8 py-8 sm:py-10">
                <h2 class="font-semibold text-gray-900 mb-4 text-lg">Email Address</h2>

                <div class="bg-white rounded-2xl p-5 flex items-center gap-4 border border-gray-100">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-gray-800 font-medium truncate">{{ $user->email }}</div>
                        <span class="inline-block mt-2 text-xs font-semibold px-3 py-1.5 rounded-full
                            {{ $user->isEmailVerified() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $user->isEmailVerified() ? 'Verified' : 'Not verified' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection