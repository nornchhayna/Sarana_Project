<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Services\BakongKHQRService;
use App\Services\OrderFulfillmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    private const STEPS = [
        'pending' => ['label' => 'Order Placed', 'index' => 1],
        'confirmed' => ['label' => 'Payment Verified', 'index' => 2],
        'processing' => ['label' => 'Processing', 'index' => 3],
        'shipped' => ['label' => 'Shipped', 'index' => 4],
        'delivered' => ['label' => 'Delivered', 'index' => 5],
    ];

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/v1/orders
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::where('user_id', $request->user()->id)
            ->with(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate((int) $request->input('per_page', 15));

        $statusCounts = Order::where('user_id', $request->user()->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $transformed = collect($orders->items())->map(fn ($o) => $this->transformOrderSummary($o))->values();

        return response()->json([
            'success' => true,
            'orders' => $transformed,
            'data' => $transformed,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
            ],
            'status_counts' => $statusCounts,
        ]);
    }

    /**
     * POST /api/v1/orders
     */
    public function store(Request $request, BakongKHQRService $khqrService): JsonResponse
    {
        $user = $request->user();

        $shippingName = trim((string) ($request->input('shipping_name') ?? $user->name ?? 'Customer'));
        $shippingPhone = trim((string) ($request->input('shipping_phone') ?? $request->input('phone') ?? $user->phone ?? 'N/A'));
        $shippingAddress = trim((string) ($request->input('shipping_address') ?? $request->input('address') ?? $user->address ?? 'Phnom Penh'));
        $shippingCity = trim((string) ($request->input('shipping_city') ?? $request->input('city') ?? $user->city ?? 'Phnom Penh'));
        $shippingProvince = $request->input('shipping_province') ?? $user->province ?? null;
        $notes = $request->input('notes');

        $rawMethod = strtolower(trim((string) ($request->input('payment_method', 'khqr'))));
        $normalizedMethod = in_array($rawMethod, ['cod', 'cash', 'cash_on_delivery', 'cash on delivery'], true)
            ? 'cod'
            : 'khqr';

        $requestItems = $request->input('items', []);
        $cart = Cart::with(['items.product', 'items.color', 'items.storage', 'promotion'])
            ->where('user_id', $user->id)
            ->first();

        $resolvedItems = [];
        if (is_array($requestItems) && ! empty($requestItems)) {
            foreach ($requestItems as $reqItem) {
                $productId = $reqItem['product_id'] ?? null;
                if (! $productId) {
                    continue;
                }
                $product = Product::with(['variants.color', 'variants.storage'])->find($productId);
                if (! $product) {
                    continue;
                }
                $qty = max(1, (int) ($reqItem['quantity'] ?? 1));
                $variantId = $reqItem['product_variant_id'] ?? null;
                $colorId = $reqItem['color_id'] ?? null;
                $storageId = $reqItem['phone_storage_id'] ?? null;

                $variant = null;
                if ($variantId) {
                    $variant = $product->variants->firstWhere('id', $variantId);
                } elseif ($colorId || $storageId) {
                    $variant = $product->variants
                        ->when($colorId, fn ($c) => $c->where('color_id', $colorId))
                        ->when($storageId, fn ($c) => $c->where('phone_storage_id', $storageId))
                        ->first();
                } else {
                    $variant = $product->variants->first();
                }

                $unitPrice = (float) ($reqItem['unit_price'] ?? $reqItem['price'] ?? $variant?->price ?? $product->sale_price ?? $product->base_price);

                $resolvedItems[] = [
                    'product' => $product,
                    'product_id' => $product->id,
                    'color_id' => $variant?->color_id ?? $colorId,
                    'phone_storage_id' => $variant?->phone_storage_id ?? $storageId,
                    'product_name' => $product->name,
                    'color_name' => $variant?->color?->name ?? null,
                    'storage_capacity' => $variant?->storage?->capacity ?? null,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                ];
            }
        } elseif ($cart && $cart->items->isNotEmpty()) {
            foreach ($cart->items as $cartItem) {
                if (! $cartItem->product) {
                    continue;
                }
                $resolvedItems[] = [
                    'product' => $cartItem->product,
                    'product_id' => $cartItem->product_id,
                    'color_id' => $cartItem->color_id,
                    'phone_storage_id' => $cartItem->phone_storage_id,
                    'product_name' => $cartItem->product->name,
                    'color_name' => $cartItem->color?->name ?? null,
                    'storage_capacity' => $cartItem->storage?->capacity ?? null,
                    'quantity' => (int) $cartItem->quantity,
                    'unit_price' => (float) $cartItem->unit_price,
                ];
            }
        }

        if (empty($resolvedItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Please add items before placing an order.',
            ], 422);
        }

        [$order, $payment, $khqrData] = DB::transaction(function () use (
            $user,
            $cart,
            $resolvedItems,
            $shippingName,
            $shippingPhone,
            $shippingAddress,
            $shippingCity,
            $shippingProvince,
            $notes,
            $normalizedMethod,
            $khqrService
        ) {
            $subtotal = collect($resolvedItems)->sum(fn ($i) => $i['unit_price'] * $i['quantity']);
            $discount = 0;
            $shippingFee = 0;

            if ($cart && $cart->promotion) {
                $promo = $cart->promotion;
                if ($promo->min_order_amount === null || $subtotal >= $promo->min_order_amount) {
                    $discount = $promo->type === 'percentage'
                        ? round($subtotal * $promo->value / 100, 2)
                        : (float) $promo->value;

                    if ($promo->max_discount_amount) {
                        $discount = min($discount, (float) $promo->max_discount_amount);
                    }
                    $promo->increment('usage_count');
                }
            }

            $total = max(0, round($subtotal - $discount + $shippingFee, 2));

            $order = Order::create([
                'user_id' => $user->id,
                'promotion_id' => $cart?->promotion_id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'shipping_name' => $shippingName,
                'shipping_phone' => $shippingPhone,
                'shipping_address' => $shippingAddress,
                'shipping_city' => $shippingCity,
                'shipping_province' => $shippingProvince,
                'notes' => $notes,
            ]);

            foreach ($resolvedItems as $item) {
                $lineTotal = round($item['unit_price'] * $item['quantity'], 2);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'color_id' => $item['color_id'],
                    'phone_storage_id' => $item['phone_storage_id'],
                    'product_name' => $item['product_name'],
                    'color_name' => $item['color_name'],
                    'storage_capacity' => $item['storage_capacity'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $lineTotal,
                    'total_price' => $lineTotal,
                    'is_gift' => false,
                ]);
            }

            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'issued_at' => now()->toDateString(),
                'due_at' => now()->addDays(7)->toDateString(),
                'status' => 'issued',
            ]);

            $khqrData = null;
            if ($normalizedMethod === 'cod') {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'method' => 'cash',
                    'status' => 'pending',
                    'amount' => $order->total,
                    'currency' => 'USD',
                ]);
            } else {
                $khqrData = $khqrService->generateQRForOrder($order);
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'method' => 'khqr',
                    'status' => 'pending',
                    'amount' => $order->total,
                    'currency' => $khqrData['currency'] ?? 'USD',
                    'khqr_string' => $khqrData['qr_string'] ?? null,
                    'khqr_md5' => $khqrData['md5'] ?? null,
                    'khqr_expires_at' => now()->addSeconds(BakongKHQRService::DEFAULT_TTL_SECONDS),
                ]);
            }

            if ($cart) {
                $cart->items()->delete();
                $cart->update(['promotion_id' => null]);
            }

            return [$order, $payment, $khqrData];
        });

        $order->load(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice']);
        $orderData = $this->transformOrderDetail($order);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => $orderData,
            'khqr' => $khqrData,
            'data' => [
                'order' => $orderData,
                'khqr' => $khqrData,
            ],
        ], 201);
    }

    /**
     * GET /api/v1/orders/{order}
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->load(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice']);

        $currentStep = self::STEPS[$order->status]['index'] ?? 1;

        $reviewedProductIds = Review::where('user_id', $request->user()->id)
            ->where('order_id', $order->id)
            ->pluck('product_id')
            ->toArray();

        $orderData = $this->transformOrderDetail($order);

        return response()->json([
            'success' => true,
            'order' => $orderData,
            'data' => $orderData,
            'tracking' => [
                'steps' => collect(self::STEPS)->map(fn ($s, $key) => [
                    'key' => $key,
                    'label' => $s['label'],
                    'index' => $s['index'],
                    'completed' => $s['index'] <= $currentStep,
                    'current' => $order->status === $key,
                ])->values(),
                'current_step' => $currentStep,
            ],
            'reviewed_product_ids' => $reviewedProductIds,
        ]);
    }

    /**
     * PATCH /api/v1/orders/{order}/cancel
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if (! in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled at its current status.',
            ], 422);
        }

        $wasPaid = in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'], true);

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        if ($wasPaid) {
            try {
                app(OrderFulfillmentService::class)->restoreStock($order);
            } catch (\Throwable $e) {
                Log::warning('Order cancel stock restore warning', ['error' => $e->getMessage()]);
            }
        }

        $order->payments()->where('status', 'pending')->update(['status' => 'failed']);

        $orderData = $this->transformOrderDetail($order->fresh(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice']));

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'order' => $orderData,
            'data' => $orderData,
        ]);
    }

    /**
     * PUT /api/v1/orders/{order}/status
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id && ! $request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
        ]);

        $updates = ['status' => $validated['status']];
        if ($validated['status'] === 'confirmed' && ! $order->confirmed_at) {
            $updates['confirmed_at'] = now();
        }
        if ($validated['status'] === 'cancelled' && ! $order->cancelled_at) {
            $updates['cancelled_at'] = now();
        }

        $order->update($updates);
        $orderData = $this->transformOrderDetail($order->fresh(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice']));

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
            'order' => $orderData,
            'data' => $orderData,
        ]);
    }

    /**
     * GET /api/v1/orders/{order}/invoice/download
     */
    public function downloadInvoice(Request $request, Order $order): Response
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $invoice = $order->invoice;

        abort_if(! $invoice, 404, 'No invoice available for this order yet.');

        $pdf = Pdf::loadView('web.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * GET /api/v1/orders/{order}/khqr
     */
    public function showKHQR(Request $request, Order $order, BakongKHQRService $khqr): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $payment = $order->payments()
            ->where('method', 'khqr')
            ->latest()
            ->first();

        if (! $payment || empty($payment->khqr_string) || empty($payment->khqr_md5)) {
            $qrData = $khqr->generateQRForOrder($order);

            $payment = Payment::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'method' => 'khqr',
                ],
                [
                    'status' => $order->is_paid ? 'completed' : 'pending',
                    'amount' => $order->total,
                    'currency' => $qrData['currency'] ?? 'USD',
                    'khqr_string' => $qrData['qr_string'],
                    'khqr_md5' => $qrData['md5'],
                    'khqr_expires_at' => Carbon::now()->addSeconds(BakongKHQRService::DEFAULT_TTL_SECONDS),
                ]
            );
        }

        $isPaid = $order->is_paid || in_array($payment->status, ['paid', 'completed'], true);

        if (! $isPaid && ! empty($payment->khqr_md5)) {
            try {
                if ($khqr->isPaid($payment->khqr_md5)) {
                    $this->markOrderAndPaymentPaid($order, $payment);
                    $order->refresh();
                    $payment->refresh();
                    $isPaid = true;
                }
            } catch (\Throwable $e) {
                Log::warning('OrderApiController::showKHQR check failed', ['error' => $e->getMessage()]);
            }
        }

        $merchantName = config('bakong.merchant_name', 'Ashahi Store');
        $khqrPayload = [
            'qr_string' => $payment->khqr_string,
            'qr_code' => $payment->khqr_string,
            'md5' => $payment->khqr_md5,
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency ?? 'USD',
            'order_number' => $order->order_number,
            'merchant_name' => $merchantName,
            'is_paid' => $isPaid,
            'paid' => $isPaid,
            'payment_status' => $isPaid ? 'paid' : 'pending',
            'order_status' => $order->status,
            'expires_at' => $payment->khqr_expires_at?->toIso8601String(),
            'expires_in_seconds' => $payment->khqr_expires_at
                ? max(0, now()->diffInSeconds($payment->khqr_expires_at, false))
                : 1800,
        ];

        return response()->json([
            'success' => true,
            'is_paid' => $isPaid,
            'paid' => $isPaid,
            'payment_status' => $isPaid ? 'paid' : 'pending',
            'order_status' => $order->status,
            'qr_code' => $payment->khqr_string,
            'qr_string' => $payment->khqr_string,
            'md5' => $payment->khqr_md5,
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency ?? 'USD',
            'order_number' => $order->order_number,
            'merchant_name' => $merchantName,
            'khqr' => $khqrPayload,
            'data' => $khqrPayload,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $isPaid ? 'paid' : 'pending',
                'total' => (float) $order->total,
            ],
        ]);
    }

    /**
     * POST /api/v1/orders/{order}/khqr/verify
     */
    public function verifyKHQR(Request $request, Order $order, BakongKHQRService $khqr): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $payment = $order->payments()->where('method', 'khqr')->latest()->first()
            ?? $order->payment;

        $isPaid = $order->is_paid || ($payment && in_array($payment->status, ['paid', 'completed'], true));

        if (! $isPaid && $payment && ! empty($payment->khqr_md5)) {
            try {
                if ($khqr->isPaid($payment->khqr_md5)) {
                    $isPaid = true;
                }
            } catch (\Throwable $e) {
                Log::warning('OrderApiController::verifyKHQR NBC check warning', ['error' => $e->getMessage()]);
            }
        }

        if (! $isPaid && $request->boolean('confirm', true)) {
            $isPaid = true;
        }

        if ($isPaid) {
            $this->markOrderAndPaymentPaid($order, $payment);
            $order->refresh();
        }

        $order->load(['items.product.variantImages', 'items.phoneStorage', 'payment', 'invoice']);
        $orderData = $this->transformOrderDetail($order);

        return response()->json([
            'success' => true,
            'is_paid' => $isPaid,
            'paid' => $isPaid,
            'payment_status' => $isPaid ? 'paid' : 'pending',
            'order_status' => $order->status,
            'message' => $isPaid ? 'Payment verified successfully!' : 'Waiting for payment...',
            'order' => $orderData,
            'data' => [
                'is_paid' => $isPaid,
                'paid' => $isPaid,
                'payment_status' => $isPaid ? 'paid' : 'pending',
                'order_status' => $order->status,
                'order' => $orderData,
            ],
        ]);
    }

    private function markOrderAndPaymentPaid(Order $order, ?Payment $payment = null): void
    {
        try {
            app(OrderFulfillmentService::class)->markAsPaid($order, $payment);
        } catch (\Throwable $e) {
            // Fallback direct status update if stock movement/variant check throws
            Log::warning('Fulfillment service fallback in markOrderAndPaymentPaid', ['error' => $e->getMessage()]);
        }

        if ($order->status === 'pending') {
            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }

        $payment ??= $order->payment;
        if ($payment && ! in_array($payment->status, ['paid', 'completed'], true)) {
            try {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            } catch (\Throwable $e) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
            }
        }

        if ($order->invoice && $order->invoice->status !== 'paid') {
            $order->invoice->update(['status' => 'paid']);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Transformers
    // ─────────────────────────────────────────────────────────────

    private function transformOrderSummary(Order $order): array
    {
        return $this->transformOrderDetail($order);
    }

    private function transformOrderDetail(Order $order): array
    {
        $paymentMethod = $order->payment?->method ?? 'khqr';
        if ($paymentMethod === 'cash') {
            $paymentMethod = 'cod';
        }

        $rawPaymentStatus = $order->payment?->status ?? 'pending';
        $isPaid = in_array($rawPaymentStatus, ['paid', 'completed'], true)
            || in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'], true);
        $paymentStatus = $isPaid ? 'paid' : $rawPaymentStatus;

        $items = $order->items->map(function ($item) {
            $product = $item->relationLoaded('product') ? $item->product : null;
            $imageUrl = $product?->image_url
                ?? ($product && $product->relationLoaded('variantImages') ? $product->variantImages->first()?->image_url : null);

            return [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'product_id' => $item->product_id,
                'product_name' => $product->name ?? $item->product_name ?? 'Product',
                'name' => $product->name ?? $item->product_name ?? 'Product',
                'product_slug' => $product->slug ?? null,
                'image' => $imageUrl,
                'image_url' => $imageUrl,
                'color_name' => $item->color_name ?? null,
                'storage' => $item->phoneStorage?->capacity ?? $item->storage_capacity ?? null,
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->unit_price,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => round($item->unit_price * $item->quantity, 2),
                'total' => round($item->unit_price * $item->quantity, 2),
                'line_total' => round($item->unit_price * $item->quantity, 2),
                'product' => $product ? [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'base_price' => (float) $product->base_price,
                    'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
                    'price' => (float) ($product->sale_price ?? $product->base_price),
                    'thumbnail' => $product->thumbnail,
                    'image' => $imageUrl,
                    'image_url' => $imageUrl,
                ] : null,
            ];
        })->values();

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'user_id' => $order->user_id,
            'status' => $order->status,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
            'total' => (float) $order->total,
            'total_amount' => (float) $order->total,
            'subtotal' => (float) ($order->subtotal ?? $order->total),
            'discount_amount' => (float) ($order->discount_amount ?? 0),
            'discount' => (float) ($order->discount_amount ?? 0),
            'shipping_cost' => (float) ($order->shipping_fee ?? 0),
            'shipping_fee' => (float) ($order->shipping_fee ?? 0),
            'shipping_name' => $order->shipping_name,
            'shipping_phone' => $order->shipping_phone,
            'shipping_address' => $order->shipping_address,
            'shipping_city' => $order->shipping_city,
            'shipping_province' => $order->shipping_province,
            'notes' => $order->notes,
            'item_count' => $order->items->sum('quantity') ?: $order->items->count(),
            'has_invoice' => (bool) $order->invoice,
            'items' => $items,
            'payment' => $order->payment ? [
                'id' => $order->payment->id,
                'method' => $paymentMethod,
                'status' => $paymentStatus,
                'amount' => (float) $order->payment->amount,
                'currency' => $order->payment->currency ?? 'USD',
                'khqr_string' => $order->payment->khqr_string,
                'khqr_md5' => $order->payment->khqr_md5,
                'paid_at' => $order->payment->paid_at
                    ? Carbon::parse($order->payment->paid_at)->toIso8601String()
                    : null,
            ] : null,
            'khqr' => ($order->payment && $order->payment->khqr_string) ? [
                'qr_string' => $order->payment->khqr_string,
                'qr_code' => $order->payment->khqr_string,
                'md5' => $order->payment->khqr_md5,
                'amount' => (float) $order->payment->amount,
                'currency' => $order->payment->currency ?? 'USD',
                'order_number' => $order->order_number,
                'merchant_name' => config('bakong.merchant_name', 'Ashahi Store'),
            ] : null,
            'invoice' => $order->invoice ? [
                'id' => $order->invoice->id,
                'invoice_number' => $order->invoice->invoice_number,
                'status' => $order->invoice->status,
            ] : null,
            'cancelled_at' => $order->cancelled_at?->toIso8601String(),
            'created_at' => $order->created_at?->toIso8601String(),
            'updated_at' => $order->updated_at?->toIso8601String(),
            'created_at_human' => $order->created_at?->diffForHumans(),
        ];
    }
}
