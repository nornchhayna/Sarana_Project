<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFulfillmentAndProductColorsTest extends TestCase
{
    use RefreshDatabase;

    private function createTestOrder(User $user, string $status = 'pending'): Order
    {
        return Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'status' => $status,
            'shipping_name' => $user->name,
            'shipping_phone' => '012345678',
            'shipping_address' => 'St 123, Phnom Penh',
            'shipping_city' => 'Phnom Penh',
            'shipping_province' => 'Phnom Penh',
            'subtotal' => 100.00,
            'shipping_fee' => 0.00,
            'discount_amount' => 0.00,
            'total' => 100.00,
        ]);
    }

    public function test_payment_status_endpoint_auto_confirms_pending_order_when_paid(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = $this->createTestOrder($user, 'pending');

        Payment::create([
            'order_id' => $order->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'method' => 'khqr',
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => 'TXN-TEST-12345',
        ]);

        $response = $this->actingAs($user)->getJson(route('checkout.payment.status', $order));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'confirmed',
            'paid' => true,
        ]);

        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    public function test_customer_order_show_auto_confirms_pending_order_if_payment_is_paid(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = $this->createTestOrder($user, 'pending');

        Payment::create([
            'order_id' => $order->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'method' => 'khqr',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('orders.show', $order));

        $response->assertStatus(200);
        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    public function test_admin_payment_approve_confirms_order_and_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->createTestOrder($customer, 'pending');

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.payments.approve', $payment));

        $response->assertRedirect();
        $this->assertEquals('paid', $payment->fresh()->status);
        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    public function test_product_detail_page_shows_colors_configured_on_variants(): void
    {
        $product = Product::factory()->create([
            'name' => 'Super Phone Pro',
            'is_active' => true,
        ]);

        $color1 = Color::factory()->create(['name' => 'Midnight Blue', 'hex_code' => '#003366']);
        $color2 = Color::factory()->create(['name' => 'Space Black', 'hex_code' => '#111111']);

        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color1->id,
            'is_active' => true,
            'stock' => 5,
        ]);

        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color2->id,
            'is_active' => true,
            'stock' => 5,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Midnight Blue');
        $response->assertSee('Space Black');
        $response->assertSee('variant-swatch');
    }
}
