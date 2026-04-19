<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // --- /users (admin-only web route) ---

    public function test_admin_can_get_users_index(): void
    {
        // Arrange
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Act
        $response = $this->actingAs($admin)->get('/users');

        // Assert
        $response->assertStatus(200);
    }

    public function test_staff_cannot_get_users_index_returns_403(): void
    {
        // Arrange
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        // Act
        $response = $this->actingAs($staff)->get('/users');

        // Assert
        $response->assertStatus(403);
    }

    public function test_viewer_cannot_get_users_index_returns_403(): void
    {
        // Arrange
        $viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);

        // Act
        $response = $this->actingAs($viewer)->get('/users');

        // Assert
        $response->assertStatus(403);
    }

    // --- POST suppliers (staff middleware on web route) ---

    public function test_staff_can_post_create_supplier(): void
    {
        // Arrange
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        // Act
        $response = $this->actingAs($staff)->post('/suppliers', [
            'name'   => 'Staff Supplier',
            'status' => 'Active',
        ]);

        // Assert: redirect after successful create (not 403/422)
        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'Staff Supplier']);
    }

    public function test_viewer_cannot_post_create_supplier_returns_403(): void
    {
        // Arrange
        $viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);

        // Act
        $response = $this->actingAs($viewer)->post('/suppliers', [
            'name'   => 'Viewer Supplier',
            'status' => 'Active',
        ]);

        // Assert
        $response->assertStatus(403);
    }

    public function test_viewer_can_get_suppliers_index_returns_200(): void
    {
        // Arrange
        $viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);

        // Act
        $response = $this->actingAs($viewer)->get('/suppliers');

        // Assert
        $response->assertStatus(200);
    }
}
