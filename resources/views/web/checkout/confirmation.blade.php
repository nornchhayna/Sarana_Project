@extends('layouts.app')
@section('title', 'Order Confirmation')

@section('content')
<div class="confirmation-page">
    <div class="confirmation-wrapper">
        <div class="section-card">
            <div class="success-message">
                <div class="success-icon">✓</div>
                <h1>Order Placed Successfully!</h1>
                <p>Your order has been confirmed and will be processed shortly.</p>
            </div>

            <div class="order-details">
                <h3>Order Information</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Order Number:</span>
                    <span class="detail-value">{{ $order->order_number }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">Cash on Delivery</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">${{ number_format($order->total, 2) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">{{ ucfirst($order->status) }}</span>
                </div>
            </div>

            <div class="shipping-info">
                <h3>Shipping Information</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $order->shipping_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $order->shipping_phone }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">{{ $order->shipping_address }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">City:</span>
                    <span class="detail-value">{{ $order->shipping_city }}</span>
                </div>

                @if($order->shipping_province)
                <div class="detail-row">
                    <span class="detail-label">Province:</span>
                    <span class="detail-value">{{ $order->shipping_province }}</span>
                </div>
                @endif
            </div>

            <div class="order-items">
                <h3>Order Items</h3>
                
                @foreach($order->items as $item)
                @if(!$item->is_gift)
                <div class="item-row">
                    <div class="item-info">
                        <span class="item-name">{{ $item->product_name }}</span>
                        @if($item->color_name || $item->storage_capacity)
                        <span class="item-variant">
                            {{ $item->color_name }} @if($item->color_name && $item->storage_capacity) · @endif {{ $item->storage_capacity }}
                        </span>
                        @endif
                    </div>
                    <div class="item-qty">x{{ $item->quantity }}</div>
                    <div class="item-price">${{ number_format($item->subtotal, 2) }}</div>
                </div>
                @endif
                @endforeach
            </div>

            <div class="action-buttons">
                <a href="{{ route('products.index') }}" class="btn-secondary">Continue Shopping</a>
                <a href="{{ route('user.orders') }}" class="btn-primary">View My Orders</a>
            </div>
        </div>
    </div>
</div>

<style>
.confirmation-page {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.confirmation-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}

.success-message {
    text-align: center;
    padding: 2rem;
    border-bottom: 1px solid #eee;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: #27AE60;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin: 0 auto 1rem;
}

.success-message h1 {
    color: #2B3D5D;
    margin: 0 0 0.5rem;
}

.success-message p {
    color: #666;
    margin: 0;
}

.order-details, .shipping-info, .order-items {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
}

.order-details h3, .shipping-info h3, .order-items h3 {
    color: #2B3D5D;
    margin: 0 0 1rem;
    font-size: 1.1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f5f5f5;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #666;
    font-weight: 500;
}

.detail-value {
    color: #2B3D5D;
    font-weight: 600;
}

.item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f5f5f5;
}

.item-row:last-child {
    border-bottom: none;
}

.item-info {
    flex: 1;
}

.item-name {
    font-weight: 600;
    color: #2B3D5D;
    display: block;
}

.item-variant {
    font-size: 0.875rem;
    color: #666;
}

.item-qty {
    color: #666;
    margin: 0 1rem;
}

.item-price {
    font-weight: 600;
    color: #2B3D5D;
}

.action-buttons {
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-primary, .btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2B3D5D;
    color: white;
}

.btn-secondary {
    background: #f5f5f5;
    color: #2B3D5D;
}

.btn-primary:hover {
    background: #1a2a3a;
}

.btn-secondary:hover {
    background: #e0e0e0;
}
</style>
@endsection