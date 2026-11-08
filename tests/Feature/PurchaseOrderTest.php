<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_purchase_orders(): void
    {
        PurchaseOrder::factory()->count(3)->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get('/admin/purchase-orders');

        $response->assertStatus(200);
        $response->assertViewHas('purchaseOrders');
    }

    public function test_can_create_purchase_order(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $poData = [
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'expected_date' => now()->addDays(7)->toDateString(),
            'shipping_cost' => 50.00,
            'currency' => 'USD',
            'notes' => 'Test purchase order',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity_ordered' => 10,
                    'unit_cost' => 100.00,
                ],
            ],
        ];

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->post('/admin/purchase-orders', $poData);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('purchase_order_items', [
            'product_id' => $product->id,
            'quantity_ordered' => 10,
        ]);
    }

    public function test_can_submit_draft_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'draft']);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/purchase-orders/{$purchaseOrder->id}/submit");

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'ordered',
        ]);
    }

    public function test_cannot_submit_non_draft_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'ordered']);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/purchase-orders/{$purchaseOrder->id}/submit");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_can_cancel_purchase_order_without_receiving(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'ordered']);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/purchase-orders/{$purchaseOrder->id}/cancel");

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_cancel_partially_received_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'partially_received']);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/purchase-orders/{$purchaseOrder->id}/cancel");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'partially_received',
        ]);
    }

    public function test_cannot_cancel_received_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'received']);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/purchase-orders/{$purchaseOrder->id}/cancel");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'received',
        ]);
    }

    public function test_can_delete_draft_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'draft']);
        PurchaseOrderItem::factory()->create(['purchase_order_id' => $purchaseOrder->id]);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->delete("/admin/purchase-orders/{$purchaseOrder->id}");

        $response->assertRedirect('/admin/purchase-orders');
        $this->assertDatabaseMissing('purchase_orders', ['id' => $purchaseOrder->id]);
        $this->assertDatabaseMissing('purchase_order_items', ['purchase_order_id' => $purchaseOrder->id]);
    }

    public function test_refresh_status_updates_to_partially_received(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item1 = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'quantity_ordered' => 10,
            'quantity_received' => 5,
        ]);
        $item2 = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'quantity_ordered' => 10,
            'quantity_received' => 0,
        ]);

        $purchaseOrder->refreshStatus();

        $this->assertEquals('partially_received', $purchaseOrder->fresh()->status);
    }

    public function test_refresh_status_updates_to_received(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item1 = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'quantity_ordered' => 10,
            'quantity_received' => 10,
        ]);
        $item2 = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'quantity_ordered' => 10,
            'quantity_received' => 10,
        ]);

        $purchaseOrder->refreshStatus();

        $this->assertEquals('received', $purchaseOrder->fresh()->status);
    }

    public function test_po_number_generation(): void
    {
        $existingPO = PurchaseOrder::factory()->create([
            'po_number' => 'PO-2026-0001',
            'created_at' => now()->setYear(2026),
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $poData = [
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'expected_date' => now()->addDays(7)->toDateString(),
            'shipping_cost' => 50.00,
            'currency' => 'USD',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity_ordered' => 10,
                    'unit_cost' => 100.00,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'admin')
            ->post('/admin/purchase-orders', $poData);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'po_number' => 'PO-2026-0002',
        ]);
    }
}