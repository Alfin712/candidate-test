<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = User::factory()->create(['role' => User::ROLE_STAFF]);
        $this->actingAs($this->actor);
    }

    public function test_index_returns_paginated_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $this->getJson('/api/suppliers')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['data', 'current_page', 'per_page', 'total']]);
    }

    public function test_index_search_filters_by_name(): void
    {
        Supplier::factory()->create(['name' => 'Alpha Corp']);
        Supplier::factory()->create(['name' => 'Beta LLC']);

        $response = $this->getJson('/api/suppliers?q=Alpha')->assertOk();

        $this->assertSame(1, $response->json('data.total'));
        $this->assertSame('Alpha Corp', $response->json('data.data.0.name'));
    }

    public function test_store_creates_supplier(): void
    {
        $this->postJson('/api/suppliers', [
            'name'   => 'New Supplier',
            'code'   => 'SUP-2026-001',
            'status' => 'Active',
        ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'New Supplier');

        $this->assertDatabaseHas('suppliers', ['code' => 'SUP-2026-001']);
    }

    public function test_store_rejects_duplicate_name(): void
    {
        Supplier::factory()->create(['name' => 'Dup']);

        $this->postJson('/api/suppliers', ['name' => 'Dup'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_show_returns_supplier_with_layups_layers(): void
    {
        $supplier = Supplier::factory()->create();

        $this->getJson("/api/suppliers/{$supplier->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonStructure(['data' => ['layups']]);
    }

    public function test_update_modifies_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Old']);

        $this->putJson("/api/suppliers/{$supplier->id}", ['name' => 'Renamed'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Renamed');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Renamed']);
    }

    public function test_destroy_deletes_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->deleteJson("/api/suppliers/{$supplier->id}")->assertStatus(204);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
