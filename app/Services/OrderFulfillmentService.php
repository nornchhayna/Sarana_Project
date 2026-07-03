<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderFulfillmentService
{
    public function isPaidStatus(string $status): bool
    {
        return in_array($status, ['confirmed', 'processing', 'shipped', 'delivered'], true);
    }

    public function markAsPaid(Order $order, ?Payment $payment = null): bool
    {
        $order->refresh();

        if ($this->isPaidStatus($order->status)) {
            return true;
        }

        if ($order->status !== 'pending') {
            return false;
        }

        DB::transaction(function () use ($order, $payment) {
            $order->load('items.product');

            foreach ($order->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('color_id', $item->color_id)
                    ->where('phone_storage_id', $item->phone_storage_id)
                    ->lockForUpdate()
                    ->first();

                if (! $stock || $stock->quantity < $item->quantity) {
                    throw new RuntimeException("Insufficient stock for {$item->product_name}.");
                }
            }

            foreach ($order->items as $item) {
                Stock::where('product_id', $item->product_id)
                    ->where('color_id', $item->color_id)
                    ->where('phone_storage_id', $item->phone_storage_id)
                    ->decrement('quantity', $item->quantity);

                $item->product?->increment('total_sold', $item->quantity);
            }

            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            $payment ??= $order->payments()->where('method', 'khqr')->latest()->first();

            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        });

        return true;
    }

    public function cancelPending(Order $order): bool
    {
        $order->refresh();

        if ($order->status !== 'pending') {
            return false;
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $order->payments()->where('status', 'pending')->update(['status' => 'failed']);

        return true;
    }

    public function restoreStock(Order $order): void
    {
        $order->load('items.product');

        foreach ($order->items as $item) {
            Stock::where('product_id', $item->product_id)
                ->where('color_id', $item->color_id)
                ->where('phone_storage_id', $item->phone_storage_id)
                ->increment('quantity', $item->quantity);

            $item->product?->decrement('total_sold', $item->quantity);
        }
    }
}
