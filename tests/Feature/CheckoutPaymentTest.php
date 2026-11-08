<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_shows_payment_options(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10
        ]);
        
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $response = $this->actingAs($user)
            ->get('/checkout');

        $response->assertStatus(200);
        $response->assertSee('Payment Method');
        $response->assertSee('KHQR / Bakong');
        $response->assertSee('Cash on Delivery');
    }

    public function test_can_place_order_with_cod_payment(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10
        ]);
        
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $checkoutData = [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '0123456789',
            'shipping_address' => '123 Test Street',
            'shipping_city' => 'Phnom Penh',
            'shipping_province' => 'Kandal',
            'notes' => 'Test order',
            'payment_method' => 'cod',
        ];

        $response = $this->actingAs($user)
            ->post('/checkout', $checkoutData);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        
        $this->assertDatabaseHas('payments', [
            'method' => 'cash',
            'status' => 'pending',
        ]);
    }

    public function test_can_place_order_with_khqr_payment(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10
        ]);
        
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $checkoutData = [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '0123456789',
            'shipping_address' => '123 Test Street',
            'shipping_city' => 'Phnom Penh',
            'shipping_province' => 'Kandal',
            'notes' => 'Test order',
            'payment_method' => 'khqr',
        ];

        $response = $this->actingAs($user)
            ->post('/checkout', $checkoutData);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        
        // KHQR payment should be created
        $this->assertDatabaseHas('payments', [
            'method' => 'khqr',
            'status' => 'pending',
        ]);
    }

    public function test_payment_method_validation(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10
        ]);
        
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $checkoutData = [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '0123456789',
            'shipping_address' => '123 Test Street',
            'shipping_city' => 'Phnom Penh',
            'shipping_province' => 'Kandal',
            'notes' => 'Test order',
            'payment_method' => 'invalid_method', // Invalid payment method
        ];

        $response = $this->actingAs($user)
            ->post('/checkout', $checkoutData);

        $response->assertSessionHasErrors('payment_method');
    }

    public function test_cod_order_redirects_to_confirmation(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10
        ]);
        
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $checkoutData = [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '0123456789',
            'shipping_address' => '123 Test Street',
            'shipping_city' => 'Phnom Penh',
            'shipping_province' => 'Kandal',
            'notes' => 'Test order',
            'payment_method' => 'cod',
        ];

        $response = $this->actingAs($user)
            ->post('/checkout', $checkoutData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}