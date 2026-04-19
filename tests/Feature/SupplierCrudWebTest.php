<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for Supplier CRUD via the web (Blade) routes.
 * API CRUD is already covered in SupplierCrudTest (api routes).
 */
class SupplierCrudWebTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staff = User::factory()->create(['role' => User::ROLE_STAFF]);
    }

    public function test_staff_creates_supplier_and_row_exists_in_db(): void
    {
        // Arrange
        $payload = [
            'name'   => 'Acme Timber',
            'status' => 'Active',
        ];

        // Act
        $response = $this->actingAs($this->staff)->post('/suppliers', $payload);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'Acme Timber']);
    }

    public function test_staff_updates_supplier_and_fields_changed_in_db(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create(['name' => 'Old Name', 'status' => 'Active']);

        // Act
        $response = $this->actingAs($this->staff)->put("/suppliers/{$supplier->id}", [
            'name'   => 'New Name',
            'status' => 'Inactive',
        ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New Name', 'status' => 'Inactive']);
    }

    public function test_staff_deletes_supplier_and_row_removed_from_db(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();

        // Act
        $response = $this->actingAs($this->staff)->delete("/suppliers/{$supplier->id}");

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_validation_missing_name_returns_errors(): void
    {
        // Arrange: payload without 'name'
        $payload = ['status' => 'Active'];

        // Act
        $response = $this->actingAs($this->staff)
            ->from('/suppliers/create')
            ->post('/suppliers', $payload);

        // Assert
        $response->assertSessionHasErrors('name');
    }
}
