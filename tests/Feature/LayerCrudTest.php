<?php

namespace Tests\Feature;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = User::factory()->create(['role' => User::ROLE_STAFF]);
        $this->actingAs($this->actor);
    }

    public function test_store_creates_layer_under_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);

        $this->postJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers", [
            'layer_order' => 1,
            'thickness'   => 0.25,
            'width'       => 100,
            'angle'       => 0,
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.layer_order', 1);

        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'layer_order' => 1]);
    }

    public function test_store_rejects_duplicate_layer_order_within_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);
        Layer::factory()->create(['layup_id' => $layup->id, 'layer_order' => 1]);

        $this->postJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers", [
            'layer_order' => 1,
            'thickness'   => 0.25,
            'width'       => 100,
            'angle'       => 0,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('layer_order');
    }

    public function test_update_modifies_layer(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);
        $layer = Layer::factory()->create(['layup_id' => $layup->id, 'thickness' => 10]);

        $this->putJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers/{$layer->id}", [
            'thickness' => 20,
        ])
            ->assertOk()
            ->assertJsonPath('data.thickness', '20.00');
    }

    public function test_destroy_deletes_layer(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);
        $layer = Layer::factory()->create(['layup_id' => $layup->id]);

        $this->deleteJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers/{$layer->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }

    public function test_validation_requires_positive_thickness(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = Layup::factory()->create(['supplier_id' => $supplier->id]);

        $this->postJson("/api/suppliers/{$supplier->id}/layups/{$layup->id}/layers", [
            'layer_order' => 1,
            'thickness'   => 0,
            'width'       => 100,
            'angle'       => 0,
        ])->assertStatus(422)->assertJsonValidationErrors('thickness');
    }
}
