<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Guard: cannot demote last admin
    // -----------------------------------------------------------------------

    public function test_admin_cannot_demote_last_admin_returns_validation_error(): void
    {
        // Arrange: a migration seed inserts test@example.com as admin.
        // We use that seeded admin as the sole admin; all other admins are removed.
        \Illuminate\Support\Facades\DB::table('users')
            ->where('role', User::ROLE_ADMIN)
            ->delete();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        // Sanity: exactly 1 admin now.
        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->count());

        // Act: try to demote the only admin to staff
        $response = $this->actingAs($admin)
            ->from("/users/{$admin->id}/edit")
            ->put("/users/{$admin->id}", [
                'name'  => $admin->name,
                'email' => $admin->email,
                'role'  => User::ROLE_STAFF,
            ]);

        // Assert: controller calls back()->withErrors(['role' => ...])
        $response->assertSessionHasErrors('role');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => User::ROLE_ADMIN]);
    }

    // -----------------------------------------------------------------------
    // Guard: cannot delete last admin
    // -----------------------------------------------------------------------

    public function test_admin_cannot_delete_last_admin(): void
    {
        // Arrange: remove seeded admin so we control the sole admin.
        \Illuminate\Support\Facades\DB::table('users')
            ->where('role', User::ROLE_ADMIN)
            ->delete();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        // A second admin-level user who will perform the delete request
        // (withoutMiddleware bypasses the EnsureAdmin gate so this staff user can hit the route).
        $other = User::factory()->create(['role' => User::ROLE_STAFF]);
        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->count());

        // Act: non-admin user tries to delete the only admin (middleware bypassed for test focus)
        $response = $this->actingAs($other)
            ->withoutMiddleware(\App\Http\Middleware\EnsureAdmin::class)
            ->from('/users')
            ->delete("/users/{$admin->id}");

        // Assert: controller guard returns back()->withErrors(['user' => ...])
        $response->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    // -----------------------------------------------------------------------
    // Guard: cannot delete self
    // -----------------------------------------------------------------------

    public function test_admin_cannot_delete_self(): void
    {
        // Arrange: two admins so the last-admin guard won't trigger
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Act
        $response = $this->actingAs($admin1)->delete("/users/{$admin1->id}");

        // Assert
        $response->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin1->id]);
    }

    // -----------------------------------------------------------------------
    // Happy path: admin CAN create new user
    // -----------------------------------------------------------------------

    public function test_admin_can_create_new_user(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $payload = [
            'name'                  => 'New Staff Member',
            'email'                 => 'newstaff@example.com',
            'password'              => 'Secret12',
            'password_confirmation' => 'Secret12',
            'role'                  => User::ROLE_STAFF,
        ];

        // Act
        $response = $this->actingAs($admin)->post('/users', $payload);

        // Assert
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newstaff@example.com', 'role' => User::ROLE_STAFF]);
    }
}
