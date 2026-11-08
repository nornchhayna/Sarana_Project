<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAddTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_to_cart(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['is_active' => true]);
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10,
            'price' => 100.00
        ]);

        $response = $this->actingAs($user)
            ->post(route('cart.add'), [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'quantity' => 1,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);
        
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);
    }

    public function test_guest_user_cannot_add_to_cart(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10,
            'price' => 100.00
        ]);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('auth.login'));
    }

    public function test_can_add_to_cart_without_variant_selection(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['is_active' => true]);
        $color = \App\Models\Color::factory()->create();
        $storage = \App\Models\PhoneStorage::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'stock' => 10,
            'price' => 100.00
        ]);

        $response = $this->actingAs($user)
            ->post(route('cart.add'), [
                'product_id' => $product->id,
                'color_id' => $color->id,
                'phone_storage_id' => $storage->id,
                'quantity' => 1,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'color_id' => $color->id,
            'phone_storage_id' => $storage->id,
            'quantity' => 1,
        ]);
    }
}