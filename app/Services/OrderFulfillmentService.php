<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\StockMovement;
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

        $payment ??= $order->payments()->latest()->first();

        // If order is already in a paid status, ensure payments are also marked as paid
        if ($this->isPaidStatus($order->status)) {
            if ($payment && ! in_array($payment->status, ['paid', 'completed'], true)) {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => $payment->paid_at ?? now(),
                ]);
            }
            return true;
        }

        if ($order->status !== 'pending' && $order->status !== 'cancelled') {
            return false;
        }

        // Check payment method - only deduct stock for KHQR payments
        $shouldDeductStock = $payment && $payment->method === 'khqr';

        DB::transaction(function () use ($order, $payment, $shouldDeductStock) {
            $order->load('items.product');

            // Only deduct stock for KHQR payments
            if ($shouldDeductStock) {
                foreach ($order->items as $item) {
                    try {
                        $variant = ProductVariant::where('product_id', $item->product_id)
                            ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                            ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                            ->lockForUpdate()
                            ->first();

                        if ($variant) {
                            StockMovement::create([
                                'product_variant_id' => $variant->id,
                                'type' => 'sale',
                                'quantity' => $item->quantity,
                                'reference_type' => 'order',
                                'reference_id' => $order->id,
                                'notes' => "Sale from order #{$order->order_number} (KHQR payment)",
                                'created_by' => $order->user_id,
                            ]);
                            $variant->adjustStock($item->quantity, 'sale');

                            // Keep legacy stock table in sync
                            $legacyStock = \App\Models\Stock::where('product_id', $item->product_id)
                                ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                                ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                                ->first();
                            if ($legacyStock) {
                                $legacyStock->update([
                                    'quantity' => max(0, (int) $legacyStock->quantity - $item->quantity),
                                ]);
                            }
                        } else {
                            $stockRow = \App\Models\Stock::where('product_id', $item->product_id)
                                ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                                ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                                ->lockForUpdate()
                                ->first();
                            if (! $stockRow) {
                                $stockRow = \App\Models\Stock::where('product_id', $item->product_id)->lockForUpdate()->first();
                            }
                            if ($stockRow) {
                                $stockRow->update([
                                    'quantity' => max(0, (int) $stockRow->quantity - $item->quantity),
                                ]);
                            }
                        }

                        $item->product?->increment('total_sold', $item->quantity);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Stock deduction warning during markAsPaid: '.$e->getMessage(), [
                            'order_id' => $order->id,
                            'item_id' => $item->id,
                        ]);
                    }
                }
            }

            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            $payment ??= $order->payments()->latest()->first();

            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            // Create invoice if not yet created
            if (! $order->invoice) {
                \App\Models\Invoice::create([
                    'order_id' => $order->id,
                    'invoice_number' => 'INV-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'issued_at' => now()->toDateString(),
                    'due_at' => now()->addDays(7)->toDateString(),
                    'status' => 'paid',
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
            $variant = ProductVariant::where('product_id', $item->product_id)
                ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                ->first();

            if ($variant) {
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'sale_return',
                    'quantity' => $item->quantity,
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'notes' => "Stock restored from cancelled order #{$order->order_number}",
                    'created_by' => $order->user_id,
                ]);
                $variant->adjustStock($item->quantity, 'sale_return');

                // Keep legacy stock table in sync
                \App\Models\Stock::where('product_id', $item->product_id)
                    ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                    ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                    ->increment('quantity', $item->quantity);
            } else {
                $stockRow = \App\Models\Stock::where('product_id', $item->product_id)
                    ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                    ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                    ->first();
                if (! $stockRow) {
                    $stockRow = \App\Models\Stock::where('product_id', $item->product_id)->first();
                }
                if ($stockRow) {
                    $stockRow->increment('quantity', $item->quantity);
                }
            }

            $item->product?->decrement('total_sold', $item->quantity);
        }
    }

    public function deductStockForCOD(Order $order): bool
    {
        $order->refresh();

        // Only deduct stock if order hasn't already had stock deducted
        // Check if stock movement for this order already exists
        $existingMovement = StockMovement::where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'sale')
            ->exists();

        if ($existingMovement) {
            return false; // Stock already deducted
        }

        DB::transaction(function () use ($order) {
            $order->load('items.product');

            foreach ($order->items as $item) {
                try {
                    $variant = ProductVariant::where('product_id', $item->product_id)
                        ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                        ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                        ->lockForUpdate()
                        ->first();

                    if ($variant) {
                        StockMovement::create([
                            'product_variant_id' => $variant->id,
                            'type' => 'sale',
                            'quantity' => $item->quantity,
                            'reference_type' => 'order',
                            'reference_id' => $order->id,
                            'notes' => "Sale from order #{$order->order_number} (COD delivered)",
                            'created_by' => $order->user_id,
                        ]);
                        $variant->adjustStock($item->quantity, 'sale');

                        // Keep legacy stock table in sync
                        $legacyStock = \App\Models\Stock::where('product_id', $item->product_id)
                            ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                            ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                            ->first();
                        if ($legacyStock) {
                            $legacyStock->update([
                                'quantity' => max(0, (int) $legacyStock->quantity - $item->quantity),
                            ]);
                        }
                    } else {
                        $stockRow = \App\Models\Stock::where('product_id', $item->product_id)
                            ->when($item->color_id, fn ($q) => $q->where('color_id', $item->color_id))
                            ->when($item->phone_storage_id, fn ($q) => $q->where('phone_storage_id', $item->phone_storage_id))
                            ->lockForUpdate()
                            ->first();
                        if (! $stockRow) {
                            $stockRow = \App\Models\Stock::where('product_id', $item->product_id)->lockForUpdate()->first();
                        }
                        if ($stockRow) {
                            $stockRow->update([
                                'quantity' => max(0, (int) $stockRow->quantity - $item->quantity),
                            ]);
                        }
                    }

                    $item->product?->increment('total_sold', $item->quantity);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Stock deduction warning during deductStockForCOD: '.$e->getMessage(), [
                        'order_id' => $order->id,
                        'item_id' => $item->id,
                    ]);
                }
            }
        });

        return true;
    }
}
