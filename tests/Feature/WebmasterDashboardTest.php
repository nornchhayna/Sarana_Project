<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WebmasterDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_webmaster_dashboard(): void
    {
        $response = $this->get('/webmaster/dashboard');
        $response->assertRedirect('/webmaster/login');
    }

    public function test_customer_cannot_access_webmaster_dashboard(): void
    {
        $customer = User::where('role', 'customer')->first();
        if (! $customer) {
            $customer = User::factory()->create(['role' => 'customer']);
        }

        $response = $this->actingAs($customer, 'webmaster')->get('/webmaster/dashboard');
        $response->assertStatus(403);
    }

    public function test_webmaster_can_view_dashboard(): void
    {
        $webmaster = User::where('role', 'webmaster')->first();
        if (! $webmaster) {
            $webmaster = User::factory()->create(['role' => 'webmaster']);
        }

        $response = $this->actingAs($webmaster, 'webmaster')->get('/webmaster/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('webmaster.Dashboard');
        $response->assertViewHasAll([
            'totalRevenue',
            'totalOrders',
            'newCustomers',
            'lowStockCount',
            'chartData',
            'recentOrders',
            'topProducts',
            'activity',
        ]);
        $response->assertSee('Total Revenue');
        $response->assertSee('Recent Orders');
        $response->assertSee('Top Selling Products');
        $response->assertSee('Quick Actions');
    }

    public function test_webmaster_can_access_key_panels(): void
    {
        $webmaster = User::where('role', 'webmaster')->first();
        if (! $webmaster) {
            $webmaster = User::factory()->create(['role' => 'webmaster']);
        }

        $urls = [
            '/webmaster/products',
            '/webmaster/categories',
            '/webmaster/brands',
            '/webmaster/colors',
            '/webmaster/models',
            '/webmaster/storages',
            '/webmaster/stock',
            '/webmaster/orders',
            '/webmaster/payments',
            '/webmaster/invoices',
            '/webmaster/promotions',
            '/webmaster/sliders',
            '/webmaster/reviews',
            '/webmaster/contacts',
        ];

        foreach ($urls as $url) {
            $res = $this->actingAs($webmaster, 'webmaster')->get($url);
            $res->assertStatus(200);
        }
    }
}
