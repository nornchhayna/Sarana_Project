{{-- resources/views/components/notification-bell.blade.php --}}
{{-- Usage: <x-notification-bell /> inside your customer navbar, only when auth()->check() --}}

@php
    $isAuthenticated = false;
    foreach (['customer', 'web', 'admin', 'webmaster'] as $guard) {
        if (auth()->guard($guard)->check()) {
            $isAuthenticated = true;
            break;
        }
    }
@endphp

@if($isAuthenticated)
<div x-data="notificationBell()" x-init="init()" class="relative">

    {{-- Bell Button --}}
    <button @click="open = !open" @click.away="open = false"
            class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Badge --}}<span x-show="count > 0"
              x-text="count > 9 ? '9+' : count"
              x-cloak
              class=" absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] px-1  text-white text-[10px] font-bold rounded-full" style="background-color: #e30b0b;">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 origin-top-right">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <span x-show="count > 0" x-text="count + ' new'" class="text-xs text-blue-600 font-medium"></span>
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto">

            {{-- Loading state --}}
            <div x-show="loading" class="px-4 py-8 text-center text-sm text-gray-400">
                Loading...
            </div>

            {{-- Empty state --}}
            <div x-show="!loading && items.length === 0" class="px-4 py-8 text-center">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm text-gray-500">No new replies</p>
            </div>

            {{-- Items --}}
            <template x-for="item in items" :key="item.id">
                <a :href="item.url"
                   @click="markAsSeen(item.id)"
                   class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="item.subject"></p>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="item.reply"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="item.replied_at"></p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1.5"></span>
                    </div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50">
            <a href="{{ route('web.notifications.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">
                View all messages →
            </a>
        </div>
    </div>
</div>

<script>
    function notificationBell() {
        return {
            open: false,
            loading: true,
            count: 0,
            items: [],

            init() {
                this.fetchNotifications();
                // Poll every 30 seconds for new replies
                setInterval(() => this.fetchNotifications(), 30000);
            },

            fetchNotifications() {
                fetch('{{ route('web.notifications.unseen') }}', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.count = data.count;
                        this.items = data.items;
                        this.loading = false;
                    })
                    .catch(() => { this.loading = false; });
            },

            markAsSeen(id) {
                // Optimistically remove from list & decrement count
                this.items = this.items.filter(i => i.id !== id);
                this.count = Math.max(0, this.count - 1);
                // Actual "seen" flag is set server-side when they open the show() route
            }
        }
    }
</script>
@endif