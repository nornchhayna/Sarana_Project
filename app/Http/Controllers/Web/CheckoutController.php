<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bakong;
use App\Models\BakongMerchant;
use App\Models\Cart;
use App\Models\Invoice;
use App\Models\KhqrMerchant;
use App\Models\Merchantbakongkhqr;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\BakongKHQRService;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderFulfillmentService $fulfillment
    ) {
        $this->middleware('auth');
    }

    private function getBakongMerchant(): ?object
    {
        $candidates = [
            Merchantbakongkhqr::class,
            Merchantbakongkhqr::class,
            BakongMerchant::class,
            Bakong::class,
            KhqrMerchant::class,
        ];

        foreach ($candidates as $class) {
            if (class_exists($class)) {
                return $class::first();
            }
        }

        return null;
    }

    private function resolveQrType(Request $request): string
    {
        $type = $request->query('qr_type', $request->input('qr_type'));

        return $type === 'merchant' ? 'merchant' : 'individual';
    }

    // Helper: build KHQR data — tries DB merchant first, falls back to env
    private function buildKhqr(Order $order, string $qrType = 'individual'): array
    {
        $bakong = $this->getBakongMerchant();
        $service = app(BakongKHQRService::class);

        $params = [
            'amount' => (float) $order->total,
            'ref' => $order->order_number,
        ];

        if ($bakong && ! empty($bakong->bakongAccountID)) {
            $service->setMerchantConfig($bakong);
        }

        return $qrType === 'merchant'
            ? $service->generateKHQRMerchant($params)
            : $service->generateKHQR($params);
    }

    private function storeKhqrPayment(Order $order, array $khqrData): Payment
    {
        $currency = config('bakong.currency', 840);

        return Payment::updateOrCreate(
            [
                'order_id' => $order->id,
                'method' => 'khqr',
                'status' => 'pending',
            ],
            [
                'amount' => $order->total,
                'currency' => (int) $currency === 840 ? 'USD' : 'KHR',
                'khqr_string' => $khqrData['qr_string'] ?? null,
                'khqr_md5' => $khqrData['md5'] ?? null,
                'khqr_expires_at' => now()->addMinutes(30),
            ]
        );
    }

    private function pendingKhqrPayment(Order $order): ?Payment
    {
        return $order->payments()
            ->where('method', 'khqr')
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    public function index()
    {
        $cart = Cart::with(['items.product.brand', 'items.color', 'items.storage', 'items.product.variantImages', 'promotion'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $bakong = $this->getBakongMerchant();

        return view('web.checkout.index', compact('cart', 'user', 'bakong'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_province' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = Cart::with(['items.product', 'items.color', 'items.storage', 'promotion'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // ── Stock check BEFORE writing anything ──────────────────────────────
        foreach ($cart->items as $item) {
            $variant = ProductVariant::where('product_id', $item->product_id)
                ->where('color_id', $item->color_id)
                ->where('phone_storage_id', $item->phone_storage_id)
                ->first();

            if (! $variant || $variant->stock < $item->quantity) {
                return back()->with(
                    'error',
                    "Insufficient stock for {$item->product->name}. Please update your cart."
                );
            }
        }

        $order = DB::transaction(function () use ($cart, $validated) {

            // ── Totals ────────────────────────────────────────────────────────
            $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
            $discount = 0;
            $shippingFee = 0;

            if ($cart->promotion) {
                $promo = $cart->promotion;
                if ($promo->min_order_amount === null || $subtotal >= $promo->min_order_amount) {
                    $discount = $promo->type === 'percentage'
                        ? round($subtotal * $promo->value / 100, 2)
                        : (float) $promo->value;

                    if ($promo->max_discount_amount) {
                        $discount = min($discount, $promo->max_discount_amount);
                    }

                    $promo->increment('usage_count');
                }
            }

            $total = max(0, $subtotal - $discount + $shippingFee);

            // ── Create order (stock is deducted only after payment) ───────────
            $order = Order::create([
                'user_id' => auth()->id(),
                'promotion_id' => $cart->promotion_id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // ── Order items (no stock change until paid) ────────────────────────
            foreach ($cart->items as $item) {
                $lineTotal = $item->unit_price * $item->quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'color_id' => $item->color_id,
                    'phone_storage_id' => $item->phone_storage_id,
                    'product_name' => $item->product->name,
                    'color_name' => $item->color?->name,
                    'storage_capacity' => $item->storage?->capacity,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $lineTotal,
                ]);
            }

            // ── Invoice ───────────────────────────────────────────────────────
            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-'.strtoupper(Str::random(10)),
                'issued_at' => now()->toDateString(),
                'due_at' => now()->addDays(7)->toDateString(),
                'status' => 'issued',
            ]);

            // ── Clear cart ────────────────────────────────────────────────────
            $cart->items()->delete();
            $cart->update(['promotion_id' => null]);

            return $order;
        });

        // ── Generate KHQR (outside transaction — no DB side effects) ─────────
        $qrType = $this->resolveQrType($request);
        $khqrData = $this->buildKhqr($order, $qrType);

        Log::info('KHQR Generated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $order->total,
            'qr_type' => $qrType,
            'success' => $khqrData['success'] ?? false,
            'has_qr_string' => ! empty($khqrData['qr_string']),
            'error' => $khqrData['error'] ?? null,
            'merchant_from_db' => ! empty($this->getBakongMerchant()),
        ]);

        if (! empty($khqrData['qr_string'])) {
            $this->storeKhqrPayment($order, $khqrData);
        }

        return redirect()
            ->route('checkout.payment', $order)
            ->with('khqr_data', $khqrData);
    }

    public function payment(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($this->fulfillment->isPaidStatus($order->status)) {
            return redirect()
                ->route('products.index')
                ->with('success', 'Payment already confirmed. Thank you for your purchase!');
        }

        if ($order->status === 'cancelled') {
            return redirect()
                ->route('products.index')
                ->with('error', 'This order was cancelled. Please place a new order.');
        }

        $order->load('items');

        $qrType = $this->resolveQrType($request);
        $khqrData = session('khqr_data');

        $cachedType = $khqrData['qr_type'] ?? 'individual';
        $needsRegen = empty($khqrData) || empty($khqrData['qr_string']) || $cachedType !== $qrType;

        if ($needsRegen) {
            $khqrData = $this->buildKhqr($order, $qrType);
            $khqrData['qr_type'] = $qrType;

            Log::info('KHQR Regenerated', [
                'order_id' => $order->id,
                'amount' => $order->total,
                'qr_type' => $qrType,
                'success' => $khqrData['success'] ?? false,
                'has_qr_string' => ! empty($khqrData['qr_string']),
                'error' => $khqrData['error'] ?? null,
            ]);

            if (! empty($khqrData['qr_string'])) {
                $this->storeKhqrPayment($order, $khqrData);
            }
        }

        $bakong = $this->getBakongMerchant();

        return view('web.payment.index', compact('order', 'khqrData', 'bakong'));
    }

    public function paymentStatus(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->refresh();

        if ($this->fulfillment->isPaidStatus($order->status)) {
            return response()->json([
                'status' => $order->status,
                'paid' => true,
                'order_number' => $order->order_number,
                'message' => 'Payment confirmed! Thank you for your purchase.',
                'redirect_url' => route('products.index'),
            ]);
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'status' => 'cancelled',
                'paid' => false,
                'order_number' => $order->order_number,
                'message' => 'This payment was cancelled.',
                'redirect_url' => route('products.index'),
            ]);
        }

        $payment = $this->pendingKhqrPayment($order);
        $md5 = $payment?->khqr_md5;

        if ($md5) {
            try {
                $paid = app(BakongKHQRService::class)->isPaid($md5);
                if ($paid) {
                    $this->fulfillment->markAsPaid($order, $payment);
                    $order->refresh();
                }
            } catch (\Throwable $e) {
                Log::warning('Payment status check failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $paid = $this->fulfillment->isPaidStatus($order->status);

        return response()->json([
            'status' => $order->status,
            'paid' => $paid,
            'order_number' => $order->order_number,
            'message' => $paid
                ? 'Payment confirmed! Thank you for your purchase.'
                : 'Waiting for payment…',
            'redirect_url' => $paid ? route('products.index') : null,
        ]);
    }

    public function cancelPayment(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This order can no longer be cancelled.',
            ], 422);
        }

        $this->fulfillment->cancelPending($order);

        return response()->json([
            'success' => true,
            'message' => 'Payment cancelled.',
            'redirect_url' => route('products.index'),
        ]);
    }

    public function bakongWebhook(Request $request)
    {
        $externalRef = $request->input('externalRef')
                    ?? $request->input('payment_reference')
                    ?? $request->input('ref');

        if (! $externalRef) {
            Log::warning('Bakong webhook: missing reference', $request->all());

            return response()->json(['message' => 'Missing reference'], 422);
        }

        $order = Order::where('order_number', $externalRef)
            ->orWhereHas('payments', fn ($q) => $q->where('khqr_md5', $externalRef))
            ->first();

        if (! $order) {
            Log::warning('Bakong webhook: order not found', ['ref' => $externalRef]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        $status = strtoupper((string) $request->input('status', 'PAID'));

        if ($status === 'PAID') {
            $payment = $this->pendingKhqrPayment($order);
            try {
                $this->fulfillment->markAsPaid($order, $payment);
            } catch (\Throwable $e) {
                Log::error('Bakong webhook: fulfillment failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);

                return response()->json(['message' => 'Fulfillment failed'], 500);
            }
        }

        Log::info('Bakong webhook processed', [
            'order_id' => $order->id,
            'ref' => $externalRef,
            'new_status' => $order->fresh()->status,
        ]);

        return response()->json(['message' => 'OK']);
    }
}
