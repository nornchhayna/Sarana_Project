<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminWebmasterManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    private function createWebmaster(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_WEBMASTER,
            'is_active' => true,
        ], $attributes));
    }

    public function test_admin_can_view_webmasters_index(): void
    {
        $admin = $this->createAdmin();
        $webmaster1 = $this->createWebmaster(['name' => 'Alice Webmaster', 'email' => 'alice@test.com']);
        $webmaster2 = $this->createWebmaster(['name' => 'Bob Webmaster', 'email' => 'bob@test.com']);
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'name' => 'Charlie Customer']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.webmasters.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.webmasters.index');
        $response->assertSee('Alice Webmaster');
        $response->assertSee('Bob Webmaster');
        $response->assertDontSee('Charlie Customer');
    }

    public function test_admin_can_filter_and_search_webmasters(): void
    {
        $admin = $this->createAdmin();
        $active = $this->createWebmaster(['name' => 'Active Officer', 'email' => 'active@example.com', 'is_active' => true]);
        $suspended = $this->createWebmaster(['name' => 'Suspended Staff', 'email' => 'suspended@example.com', 'is_active' => false]);

        // Search test
        $response = $this->actingAs($admin, 'admin')->get(route('admin.webmasters.index', ['search' => 'Active Officer']));
        $response->assertStatus(200);
        $response->assertSee('Active Officer');
        $response->assertDontSee('Suspended Staff');

        // Status filter test
        $response = $this->actingAs($admin, 'admin')->get(route('admin.webmasters.index', ['status' => 'inactive']));
        $response->assertStatus(200);
        $response->assertSee('Suspended Staff');
        $response->assertDontSee('Active Officer');
    }

    public function test_admin_can_view_create_webmaster_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.webmasters.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.webmasters.create');
        $response->assertSee('Create Webmaster Account');
    }

    public function test_admin_can_create_a_new_webmaster(): void
    {
        $admin = $this->createAdmin();

        $data = [
            'name' => 'David Manager',
            'email' => 'david@webmaster.com',
            'phone' => '0987654321',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'gender' => 'male',
            'city' => 'Phnom Penh',
            'province' => 'Phnom Penh',
            'address' => 'St 271, Sangkat Boeung Tumpun',
            'is_active' => '1',
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.webmasters.store'), $data);

        $response->assertRedirect(route('admin.webmasters.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'david@webmaster.com',
            'name' => 'David Manager',
            'role' => User::ROLE_WEBMASTER,
            'is_active' => true,
        ]);

        $created = User::where('email', 'david@webmaster.com')->first();
        $this->assertTrue(Hash::check('secret12345', $created->password));
    }

    public function test_admin_can_view_webmaster_show_page(): void
    {
        $admin = $this->createAdmin();
        $webmaster = $this->createWebmaster(['name' => 'Eva Tech', 'email' => 'eva@test.com']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.webmasters.show', $webmaster));

        $response->assertStatus(200);
        $response->assertViewIs('admin.webmasters.show');
        $response->assertSee('Eva Tech');
        $response->assertSee('eva@test.com');
    }

    public function test_admin_can_view_edit_webmaster_page(): void
    {
        $admin = $this->createAdmin();
        $webmaster = $this->createWebmaster();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.webmasters.edit', $webmaster));

        $response->assertStatus(200);
        $response->assertViewIs('admin.webmasters.edit');
        $response->assertSee($webmaster->name);
    }

    public function test_admin_can_update_webmaster(): void
    {
        $admin = $this->createAdmin();
        $webmaster = $this->createWebmaster(['name' => 'Old Name', 'email' => 'old@webmaster.com']);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'new@webmaster.com',
            'phone' => '011223344',
            'gender' => 'female',
            'city' => 'Siem Reap',
            'province' => 'Siem Reap',
            'address' => 'Pub Street',
            'is_active' => '1',
            'password' => 'newsecretpassword',
            'password_confirmation' => 'newsecretpassword',
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.webmasters.update', $webmaster), $updateData);

        $response->assertRedirect(route('admin.webmasters.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $webmaster->id,
            'name' => 'Updated Name',
            'email' => 'new@webmaster.com',
            'phone' => '011223344',
            'role' => User::ROLE_WEBMASTER,
        ]);

        $webmaster->refresh();
        $this->assertTrue(Hash::check('newsecretpassword', $webmaster->password));
    }

    public function test_admin_can_toggle_webmaster_status(): void
    {
        $admin = $this->createAdmin();
        $webmaster = $this->createWebmaster(['is_active' => true]);

        // Suspend
        $response = $this->actingAs($admin, 'admin')->patch(route('admin.webmasters.toggleStatus', $webmaster));
        $response->assertRedirect();
        $webmaster->refresh();
        $this->assertFalse($webmaster->is_active);

        // Reactivate
        $response = $this->actingAs($admin, 'admin')->patch(route('admin.webmasters.toggleStatus', $webmaster));
        $response->assertRedirect();
        $webmaster->refresh();
        $this->assertTrue($webmaster->is_active);
    }

    public function test_admin_cannot_toggle_own_status(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.webmasters.toggleStatus', $admin));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_delete_webmaster(): void
    {
        $admin = $this->createAdmin();
        $webmaster = $this->createWebmaster();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.webmasters.destroy', $webmaster));

        $response->assertRedirect(route('admin.webmasters.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $webmaster->id]);
    }

    public function test_unauthenticated_user_cannot_access_webmaster_management(): void
    {
        $response = $this->get(route('admin.webmasters.index'));
        $response->assertRedirect(route('admin.login'));
    }
}
