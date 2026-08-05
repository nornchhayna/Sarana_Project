{{-- resources/views/web/notifications/show.blade.php --}}
@extends('layouts.app')

@section('title', 'My Message')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Back link --}}
    <a href="{{ route('notifications.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to My Messages
    </a>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Subject</p>
                    <h1 class="text-lg font-bold text-gray-900">{{ $contact->subject ?? 'Your Enquiry' }}</h1>
                </div>

                @if($contact->status === 'replied')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Replied
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 flex-shrink-0">
                        Pending
                    </span>
                @endif
            </div>
        </div>

        {{-- Original message (sent by customer) --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <p class="text-sm font-semibold text-gray-900">You</p>
                        <p class="text-xs text-gray-400">{{ $contact->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="mt-2 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $contact->message }}</div>
                </div>
            </div>
        </div>

        {{-- Reply (from support team) --}}
        @if($contact->status === 'replied' && $contact->reply)
            <div class="px-6 py-5 bg-blue-50/40">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 text-white flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-900">Support Team</p>
                            @if($contact->replied_at)
                                <p class="text-xs text-gray-400">{{ $contact->replied_at->format('M d, Y \a\t h:i A') }}</p>
                            @endif
                        </div>
                        <div class="mt-2 bg-white border border-blue-100 rounded-xl px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed shadow-sm">{{ $contact->reply }}</div>
                    </div>
                </div>
            </div>
        @else
            {{-- Waiting for reply --}}
            <div class="px-6 py-8 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Waiting for a reply</p>
                <p class="text-xs text-gray-500 mt-1">Our team typically responds within 24-48 hours.</p>
            </div>
        @endif
    </div>

    {{-- Contact us again --}}
    @if (Route::has('contact.index'))
        <div class="mt-6 text-center">
            <a href="{{ route('contact.index') }}"
               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Send another message
            </a>
        </div>
    @endif
</div>
@endsection