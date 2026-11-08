<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_users_index(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['name' => 'John Customer']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertSee('John Customer');
    }

    public function test_admin_can_view_user_create_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    public function test_admin_can_create_user_with_webmaster_role(): void
    {
        $admin = $this->createAdmin();

        $data = [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'phone' => '012999888',
            'role' => 'webmaster',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'is_active' => '1',
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.users.store'), $data);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role' => 'webmaster',
        ]);
    }

    public function test_admin_can_view_and_edit_user(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['role' => 'customer']);

        $editResponse = $this->actingAs($admin, 'admin')->get(route('admin.users.edit', $user));
        $editResponse->assertStatus(200);
        $editResponse->assertViewIs('admin.users.edit');

        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.users.update', $user), [
            'name' => 'Promoted To Webmaster',
            'email' => $user->email,
            'role' => 'webmaster',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.users.show', $user));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Promoted To Webmaster',
            'role' => 'webmaster',
        ]);
    }
}
