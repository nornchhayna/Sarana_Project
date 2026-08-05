{{-- resources/views/admin/contacts/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Contact Message')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Back link --}}
    <a href="{{ route('admin.contacts.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Contact Messages
    </a>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('warning') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
            <p class="text-sm font-medium mb-1">Please fix the following:</p>
            <ul class="text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Message Card --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-base font-bold flex-shrink-0">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-lg font-bold text-gray-900 truncate">{{ $contact->name }}</h1>
                            <p class="text-sm text-gray-500 truncate">{{ $contact->email }}</p>
                            @if(!empty($contact->phone))
                                <p class="text-sm text-gray-500">{{ $contact->phone }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        @if($contact->status === 'unread')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Unread
                            </span>
                        @elseif($contact->status === 'read')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Read
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Replied
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Subject & Meta --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Subject</p>
                    <p class="text-gray-900 font-semibold">{{ $contact->subject ?? 'No subject' }}</p>
                </div>

                {{-- Message Body --}}
                <div class="px-6 py-6">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Message</p>
                    <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $contact->message }}</div>
                </div>

                {{-- Timestamp footer --}}
                <div class="px-6 py-3 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Received {{ $contact->created_at->format('M d, Y \a\t h:i A') }}
                    ({{ $contact->created_at->diffForHumans() }})
                </div>
            </div>

            {{-- Existing Reply (if replied) --}}
            @if($contact->status === 'replied' && $contact->reply)
                <div class="bg-green-50 rounded-2xl border border-green-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-green-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <p class="text-sm font-semibold text-green-800">
                            Your Reply
                            @if($contact->replied_at)
                                <span class="font-normal text-green-600">— sent {{ $contact->replied_at->diffForHumans() }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $contact->reply }}</p>
                    </div>
                </div>
            @endif

            {{-- Reply Form --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">
                        {{ $contact->status === 'replied' ? 'Send Another Reply' : 'Reply to ' . $contact->name }}
                    </h2>
                </div>
                <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST" class="p-6">
                    @csrf
                    <textarea name="reply" rows="6" required minlength="10"
                              placeholder="Type your reply here..."
                              class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all resize-none">{{ old('reply') }}</textarea>

                    <div class="flex items-center justify-between mt-4">
                        <p class="text-xs text-gray-400">
                            This will be emailed directly to {{ $contact->email }}
                        </p>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Quick Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Contact Details</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div class="min-w-0">
                            <dt class="text-xs text-gray-400">Name</dt>
                            <dd class="text-gray-900 font-medium truncate">{{ $contact->name }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div class="min-w-0">
                            <dt class="text-xs text-gray-400">Email</dt>
                            <dd class="text-gray-900 font-medium truncate">
                                <a href="mailto:{{ $contact->email }}" class="hover:text-blue-600">{{ $contact->email }}</a>
                            </dd>
                        </div>
                    </div>
                    @if(!empty($contact->phone))
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div class="min-w-0">
                                <dt class="text-xs text-gray-400">Phone</dt>
                                <dd class="text-gray-900 font-medium">{{ $contact->phone }}</dd>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="min-w-0">
                            <dt class="text-xs text-gray-400">Submitted</dt>
                            <dd class="text-gray-900 font-medium">{{ $contact->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-2">
                    @if($contact->status !== 'unread')
                        <form action="{{ route('admin.contacts.mark-read', $contact) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Mark as Read
                            </button>
                        </form>
                    @endif

                    <a href="mailto:{{ $contact->email }}"
                       class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Open in Email Client
                    </a>

                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this contact message? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection