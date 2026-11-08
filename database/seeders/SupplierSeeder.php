<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();

        $suppliers = [
            [
                'name' => 'Apple Authorized Distribution Cambodia',
                'contact_person' => 'Sokha Chan',
                'phone' => '0967177746',
                'email' => 'distribution-apple@ashahi.com',
                'address' => '#128 Monivong Blvd, Sangkat Boeung Keng Kang 1',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'notes' => 'Primary tier-1 authorized distributor for Apple products and original accessories.',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Electronics Mekong Supply Co.',
                'contact_person' => 'Dara Vong',
                'phone' => '012999888',
                'email' => 'sales@samsung-mekong.com',
                'address' => '#45 Russian Federation Blvd',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'notes' => 'Direct distributor for Samsung smartphones, watches, and smart displays.',
                'is_active' => true,
            ],
            [
                'name' => 'Global Tech Accessories Ltd',
                'contact_person' => 'Vannak Seng',
                'phone' => '011234567',
                'email' => 'supply@globaltech.com',
                'address' => '#88 St. 271, Toul Kork',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'notes' => 'Authorized distributor for Anker, ESR, Baseus, and Ugreen charging accessories.',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $data) {
            $supplier = Supplier::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Create a sample purchase order if none exists for this supplier
            if ($supplier->purchaseOrders()->count() === 0 && $admin) {
                $po = PurchaseOrder::create([
                    'supplier_id' => $supplier->id,
                    'created_by' => $admin->id,
                    'po_number' => 'PO-' . strtoupper(substr(uniqid(), -8)),
                    'status' => 'received',
                    'order_date' => now()->subDays(rand(3, 15)),
                    'expected_date' => now()->subDays(rand(1, 2)),
                    'subtotal' => 5000.00,
                    'shipping_cost' => 50.00,
                    'total_amount' => 5050.00,
                    'currency' => 'USD',
                    'notes' => 'Initial stock inventory receiving for Q4 flagship launch.',
                ]);

                // Sample purchase order item
                $product = Product::first();
                if ($product) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'product_id' => $product->id,
                        'product_variant_id' => $product->variants()->first()?->id,
                        'quantity_ordered' => 20,
                        'quantity_received' => 20,
                        'unit_cost' => 250.00,
                    ]);
                }
            }
        }
    }
}
