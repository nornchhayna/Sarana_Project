<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->get('/admin/suppliers');

        $response->assertStatus(200);
        $response->assertViewHas('suppliers');
    }

    public function test_can_create_supplier(): void
    {
        $supplierData = [
            'name' => 'Test Supplier',
            'contact_person' => 'John Doe',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'country' => 'Test Country',
            'is_active' => true,
        ];

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->post('/admin/suppliers', $supplierData);

        $response->assertRedirect('/admin/suppliers');
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Test Supplier',
            'email' => 'test@example.com',
        ]);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->put("/admin/suppliers/{$supplier->id}", [
                'name' => 'Updated Supplier Name',
                'email' => 'updated@example.com',
                'is_active' => false,
            ]);

        $response->assertRedirect('/admin/suppliers');
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_can_delete_supplier_without_purchase_orders(): void
    {
        $supplier = Supplier::factory()->create();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->delete("/admin/suppliers/{$supplier->id}");

        $response->assertRedirect('/admin/suppliers');
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_can_toggle_supplier_active_status(): void
    {
        $supplier = Supplier::factory()->create(['is_active' => true]);

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/suppliers/{$supplier->id}/toggle-active");

        $response->assertRedirect('/admin/suppliers');
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'is_active' => false,
        ]);
    }

    public function test_can_restore_deleted_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $supplier->delete();

        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'admin')
            ->patch("/admin/suppliers/{$supplier->id}/restore");

        $response->assertRedirect('/admin/suppliers');
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'deleted_at' => null,
        ]);
    }
}