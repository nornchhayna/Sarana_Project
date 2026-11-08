<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_stock_movements(): void
    {
        StockMovement::factory()->count(3)->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get('/admin/stock-movements');

        $response->assertStatus(200);
        $response->assertViewHas('movements');
    }

    public function test_can_create_manual_adjustment_in(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 10]);

        $adjustmentData = [
            'product_variant_id' => $variant->id,
            'type' => 'adjustment_in',
            'quantity' => 5,
            'notes' => 'Stock recount found extra items',
        ];

        $response = $this->actingAs($user, 'admin')
            ->post('/admin/stock-movements', $adjustmentData);

        $response->assertRedirect('/admin/stock-movements');
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => 'adjustment_in',
            'quantity' => 5,
            'reference_type' => 'manual_adjustment',
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 15, // 10 + 5
        ]);
    }

    public function test_can_create_manual_adjustment_out(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 10]);

        $adjustmentData = [
            'product_variant_id' => $variant->id,
            'type' => 'adjustment_out',
            'quantity' => 3,
            'notes' => 'Damaged items removed',
        ];

        $response = $this->actingAs($user, 'admin')
            ->post('/admin/stock-movements', $adjustmentData);

        $response->assertRedirect('/admin/stock-movements');
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => 'adjustment_out',
            'quantity' => 3,
            'reference_type' => 'manual_adjustment',
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 7, // 10 - 3
        ]);
    }

    public function test_cannot_create_invalid_movement_type(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $adjustmentData = [
            'product_variant_id' => $variant->id,
            'type' => 'invalid_type',
            'quantity' => 5,
            'notes' => 'Test',
        ];

        $response = $this->actingAs($user, 'admin')
            ->post('/admin/stock-movements', $adjustmentData);

        $response->assertSessionHasErrors();
    }

    public function test_can_view_stock_movements_for_variant(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        StockMovement::factory()->count(3)->create(['product_variant_id' => $variant->id]);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get("/admin/product-variants/{$variant->id}/stock-movements");

        $response->assertStatus(200);
        $response->assertViewHas('movements');
        $response->assertViewHas('variant');
    }

    public function test_stock_movement_is_append_only(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $movement = StockMovement::factory()->create([
            'product_variant_id' => $variant->id,
            'type' => 'adjustment_in',
            'quantity' => 10,
        ]);

        // StockMovement model should not have updated_at
        $this->assertNull($movement->updated_at);
    }

    public function test_can_show_single_stock_movement(): void
    {
        $movement = StockMovement::factory()->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get("/admin/stock-movements/{$movement->id}");

        $response->assertStatus(200);
        $response->assertViewHas('movement');
    }

    public function test_purchase_receipt_increases_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 5]);

        $variant->adjustStock(10, 'purchase_receipt');

        $this->assertEquals(15, $variant->fresh()->stock);
    }

    public function test_sale_decreases_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 15]);

        $variant->adjustStock(5, 'sale');

        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_adjustment_in_increases_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 5]);

        $variant->adjustStock(3, 'adjustment_in');

        $this->assertEquals(8, $variant->fresh()->stock);
    }

    public function test_adjustment_out_decreases_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 10]);

        $variant->adjustStock(2, 'adjustment_out');

        $this->assertEquals(8, $variant->fresh()->stock);
    }

    public function test_sale_return_increases_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 5]);

        $variant->adjustStock(3, 'sale_return');

        $this->assertEquals(8, $variant->fresh()->stock);
    }

    public function test_damage_decreases_stock(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 10]);

        $variant->adjustStock(1, 'damage');

        $this->assertEquals(9, $variant->fresh()->stock);
    }
}