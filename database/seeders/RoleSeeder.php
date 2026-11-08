<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsByGroup = [
            'Products' => [
                'products.view' => 'View products',
                'products.create' => 'Create new products',
                'products.edit' => 'Edit existing products',
                'products.delete' => 'Delete products',
            ],
            'Categories' => [
                'categories.view' => 'View categories',
                'categories.create' => 'Create categories',
                'categories.edit' => 'Edit categories',
                'categories.delete' => 'Delete categories',
            ],
            'Brands' => [
                'brands.view' => 'View brands',
                'brands.create' => 'Create brands',
                'brands.edit' => 'Edit brands',
                'brands.delete' => 'Delete brands',
            ],
            'Orders' => [
                'orders.view' => 'View orders',
                'orders.edit' => 'Process and update orders',
                'orders.delete' => 'Cancel or delete orders',
            ],
            'Purchasing' => [
                'purchase_orders.view' => 'View purchase orders',
                'purchase_orders.create' => 'Create purchase orders',
                'purchase_orders.edit' => 'Edit purchase orders',
                'suppliers.manage' => 'Manage suppliers',
                'stock.manage' => 'Manage inventory & stock',
            ],
            'Content' => [
                'blogs.manage' => 'Manage blogs and articles',
                'sliders.manage' => 'Manage banner sliders',
                'reviews.manage' => 'Manage customer reviews',
                'promotions.manage' => 'Manage promo codes and discounts',
            ],
            'System' => [
                'users.manage' => 'Manage users and roles',
                'settings.manage' => 'Manage general settings',
            ],
        ];

        $allPermissionIds = [];
        foreach ($permissionsByGroup as $group => $perms) {
            foreach ($perms as $slug => $name) {
                $permission = Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'slug' => $slug,
                        'group' => $group,
                        'description' => $name,
                    ]
                );
                $allPermissionIds[] = $permission->id;
            }
        }

        // Roles
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full administrative access to the entire store.',
                'is_default' => false,
            ]
        );
        $adminRole->permissions()->sync($allPermissionIds);

        $webmasterRole = Role::updateOrCreate(
            ['slug' => 'webmaster'],
            [
                'name' => 'Webmaster',
                'slug' => 'webmaster',
                'description' => 'Technical administrator managing content and system settings.',
                'is_default' => false,
            ]
        );
        $webmasterRole->permissions()->sync($allPermissionIds);

        $staffRole = Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Store staff managing orders, products, and inventory.',
                'is_default' => false,
            ]
        );
        $staffPermissions = Permission::whereIn('slug', [
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'brands.view',
            'orders.view', 'orders.edit',
            'stock.manage', 'purchase_orders.view',
        ])->pluck('id');
        $staffRole->permissions()->sync($staffPermissions);

        $customerRole = Role::updateOrCreate(
            ['slug' => 'customer'],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Regular store customer.',
                'is_default' => true,
            ]
        );

        // Assign roles to seeded users
        $adminUsers = User::whereIn('email', ['admin@gmail.com', 'admin@example.com'])->get();
        foreach ($adminUsers as $user) {
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        $webmasterUsers = User::where('email', 'webmaster@gmail.com')->get();
        foreach ($webmasterUsers as $user) {
            $user->roles()->syncWithoutDetaching([$webmasterRole->id]);
        }

        $staffUsers = User::where('email', 'staff@gmail.com')->get();
        foreach ($staffUsers as $user) {
            $user->roles()->syncWithoutDetaching([$staffRole->id]);
        }
    }
}
