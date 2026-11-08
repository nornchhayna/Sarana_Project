<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\StockReceiving;
use App\Models\StockReceivingItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockReceivingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_stock_receivings(): void
    {
        StockReceiving::factory()->count(3)->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get('/admin/stock-receivings');

        $response->assertStatus(200);
        $response->assertViewHas('receivings');
    }

    public function test_can_receive_stock_against_purchase_order(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 0]);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
        ]);

        $purchaseOrderItem = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity_ordered' => 10,
            'quantity_received' => 0,
            'unit_cost' => 100.00,
        ]);

        $receivingData = [
            'reference_number' => 'REF-123',
            'received_at' => now()->toDateString(),
            'notes' => 'Test receiving',
            'items' => [
                [
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'quantity_received' => 5,
                    'unit_cost' => 100.00,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock_receivings', [
            'purchase_order_id' => $purchaseOrder->id,
            'reference_number' => 'REF-123',
        ]);
        $this->assertDatabaseHas('stock_receiving_items', [
            'purchase_order_item_id' => $purchaseOrderItem->id,
            'quantity_received' => 5,
        ]);
        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $purchaseOrderItem->id,
            'quantity_received' => 5,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => 'purchase_receipt',
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 5,
        ]);
    }

    public function test_cannot_receive_stock_against_draft_purchase_order(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'draft']);

        $receivingData = [
            'received_at' => now()->toDateString(),
            'items' => [],
        ];

        $response = $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        $response->assertStatus(403);
    }

    public function test_cannot_receive_stock_against_cancelled_purchase_order(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'cancelled']);

        $receivingData = [
            'received_at' => now()->toDateString(),
            'items' => [],
        ];

        $response = $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        $response->assertStatus(403);
    }

    public function test_cannot_receive_stock_against_fully_received_purchase_order(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'received']);

        $receivingData = [
            'received_at' => now()->toDateString(),
            'items' => [],
        ];

        $response = $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        $response->assertStatus(403);
    }

    public function test_purchase_order_status_updates_to_partially_received(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 0]);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
        ]);

        $purchaseOrderItem = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity_ordered' => 10,
            'quantity_received' => 0,
            'unit_cost' => 100.00,
        ]);

        $receivingData = [
            'received_at' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'quantity_received' => 5,
                    'unit_cost' => 100.00,
                ],
            ],
        ];

        $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        $this->assertEquals('partially_received', $purchaseOrder->fresh()->status);
    }

    public function test_purchase_order_status_updates_to_received(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 0]);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
        ]);

        $purchaseOrderItem = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity_ordered' => 10,
            'quantity_received' => 0,
            'unit_cost' => 100.00,
        ]);

        $receivingData = [
            'received_at' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'quantity_received' => 10,
                    'unit_cost' => 100.00,
                ],
            ],
        ];

        $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        $this->assertEquals('received', $purchaseOrder->fresh()->status);
    }

    public function test_cannot_receive_more_than_outstanding_quantity(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 0]);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
        ]);

        $purchaseOrderItem = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity_ordered' => 10,
            'quantity_received' => 5,
            'unit_cost' => 100.00,
        ]);

        $receivingData = [
            'received_at' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'quantity_received' => 10, // Try to receive 10 when only 5 outstanding
                    'unit_cost' => 100.00,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'admin')
            ->post("/admin/purchase-orders/{$purchaseOrder->id}/receivings", $receivingData);

        // Should receive max 5 (outstanding quantity)
        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $purchaseOrderItem->id,
            'quantity_received' => 10, // 5 + 5 (received up to outstanding)
        ]);
        
        // Variant stock should be updated by 5 (the actual received amount)
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 5,
        ]);
    }

    public function test_quantity_outstanding_accessor(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create([
            'quantity_ordered' => 10,
            'quantity_received' => 3,
        ]);

        $this->assertEquals(7, $purchaseOrderItem->quantity_outstanding);
    }

    public function test_can_show_stock_receiving_details(): void
    {
        $stockReceiving = StockReceiving::factory()->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get("/admin/stock-receivings/{$stockReceiving->id}");

        $response->assertStatus(200);
        $response->assertViewHas('stockReceiving');
    }
}