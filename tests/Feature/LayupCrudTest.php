<?php

namespace Tests\Feature;

use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayupCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = User::factory()->create(['role' => User::ROLE_STAFF]);
        $this->actingAs($this->actor);
    }

    public function test_store_creates_layup_under_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->postJson("/api/suppliers/{$supplier->id}/layups", [
            'name'      => 'LU-001',
            'ply_count' => 5,
            'status'    => 'Active',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'LU-001');

        $this->assertDatabaseHas('clt_layups', ['supplier_id' => $supplier->id, 'name' => 'LU-001']);
    }

    public function test_store_rejects_duplicate_name_within_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        Layup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'DUP']);

        $this->postJson("/api/suppliers/{$supplier->id}/layups", ['name' => 'DUP'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_same_layup_name_allowed_across_different_suppliers(): void
    {
        $a = Supplier::factory()->create();
        $b = Supplier::factory()->create();
        Layup::factory()->create(['supplier_id' => $a->id, 'name' => 'SAME']);

        $this->postJson("/api/suppliers/{$b->id}/layups", ['name' => 'SAME'])
            ->assertStatus(201);
    }

    public function test_show_returns_layup_with_layers(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);

        $this->getJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $layup->id)
            ->assertJsonStructure(['data' => ['layers']]);
    }

    public function test_update_modifies_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id, 'name' => 'OLD']);

        $this->putJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}", ['name' => 'NEW'])
            ->assertOk()
            ->assertJsonPath('data.name', 'NEW');
    }

    public function test_destroy_deletes_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);

        $this->deleteJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}")->assertStatus(204);

        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    public function test_show_returns_404_when_layup_belongs_to_different_supplier(): void
    {
        $a = Supplier::factory()->create();
        $b = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $a->id]);

        $this->getJson("/api/suppliers/{$b->id}/layups/{$layup->id}")->assertStatus(404);
    }
}
