@extends('layouts.app')

@section('content')


<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Orders</h1>
            <p class="text-gray-600 mt-2">View and track your orders</p>
        </div>
        <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm font-semibold">
            Continue Shopping
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="flex flex-wrap border-b">
            <a href="{{ route('orders.index') }}" class="flex items-center px-6 py-4 border-b-2 {{ !request('status') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800' }} font-semibold text-sm transition">
                All Orders <span class="ml-2 bg-gray-200 px-2 py-1 rounded text-xs font-normal">{{ $statusCounts->sum() ?? 0 }}</span>
            </a>
            @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $statusKey => $statusLabel)
                <a href="{{ route('orders.index', ['status' => $statusKey]) }}" class="flex items-center px-6 py-4 border-b-2 {{ request('status') === $statusKey ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800' }} font-semibold text-sm transition">
                    {{ $statusLabel }} 
                    @if(isset($statusCounts[$statusKey]))
                        <span class="ml-2 bg-gray-200 px-2 py-1 rounded text-xs font-normal">{{ $statusCounts[$statusKey] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 p-4 mb-6 rounded">
            <p class="font-semibold">Error!</p>
            <ul class="text-sm mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 p-4 mb-6 rounded flex justify-between items-center">
            <p>{{ session('success') }}</p>
            <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; cursor:pointer;" class="text-green-600 hover:text-green-800">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 p-4 mb-6 rounded flex justify-between items-center">
            <p>{{ session('error') }}</p>
            <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; cursor:pointer;" class="text-red-600 hover:text-red-800">✕</button>
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Order #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Items</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 font-semibold">
                                {{ $order->order_number }}
                            </a>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $order->created_at->format('M d, Y') }}
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i A') }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            @php
                                $itemCount = $order->items->count();
                                $totalQty = $order->items->sum('quantity');
                            @endphp
                            {{ $itemCount }} {{ Str::plural('item', $itemCount) }}
                            @if($totalQty > 0)
                                <div class="text-xs text-gray-500">({{ $totalQty }} total qty)</div>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">
                            {{ config('app.currency_symbol', '৳') }}{{ number_format($order->total, 2) }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-purple-100 text-purple-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                
                                $statusIcon = match($order->status) {
                                    'pending' => '⏳',
                                    'confirmed' => '✓',
                                    'processing' => '📦',
                                    'shipped' => '🚚',
                                    'delivered' => '✓',
                                    'cancelled' => '✕',
                                    default => '•'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-block">
                                {{ $statusIcon }} {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                    View
                                </a>
                                
                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this order?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($orders->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div style="font-size: 3rem;" class="text-gray-400 mb-4">📦</div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">You haven't placed any orders. Start shopping now!</p>
            <a href="{{ route('home') }}" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-semibold">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection