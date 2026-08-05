{{-- resources/views/web/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'My Messages')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Messages</h1>
        <p class="text-sm text-gray-500 mt-1">All messages you've sent and replies from our team</p>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($contacts->count() > 0)
        <div class="space-y-3">
            @foreach($contacts as $contact)
                <a href="{{ route('web.contact.show', $contact) }}"
                   class="block bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition-all {{ $contact->status === 'replied' && !$contact->is_seen_by_customer ? 'ring-2 ring-blue-100 border-blue-200' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            {{-- Status icon --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $contact->status === 'replied' ? 'bg-green-100 text-green-600' : ($contact->status === 'read' ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-600') }}">
                                @if($contact->status === 'replied')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-gray-900 truncate">
                                        {{ $contact->subject ?? 'No subject' }}
                                    </p>
                                    @if($contact->status === 'replied' && !$contact->is_seen_by_customer)
                                        <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 truncate mt-0.5">{{ $contact->message }}</p>
                            </div>
                        </div>

                        {{-- Status badge + date --}}
                        <div class="text-right flex-shrink-0">
                            @if($contact->status === 'replied')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Replied
                                </span>
                            @elseif($contact->status === 'read')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Read
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    Pending
                                </span>
                            @endif
                            <p class="text-xs text-gray-400 mt-1.5">{{ $contact->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $contacts->links() }}
        </div>
    @else
        {{-- Empty state --}}
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-200">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-900 font-medium">No messages yet</p>
            <p class="text-gray-500 text-sm mt-1">Messages you send us will appear here</p>

            @if(Route::has('contact.index'))
                <a href="{{ route('contact.index') }}"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                    Send a Message
                </a>
            @endif
        </div>
    @endif
</div>
@endsection