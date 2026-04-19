<?php

namespace Tests\Feature;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = User::factory()->create(['role' => User::ROLE_STAFF]);
        $this->actingAs($this->actor);
    }

    public function test_it_exports_supplier_with_nested_layups_and_layers(): void
    {
        $supplier = Supplier::create([
            'name'   => 'Nordic Structures Inc.',
            'status' => 'Active',
        ]);

        $layup = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-5-150-L',
            'ply_count'   => 5,
            'status'      => 'Active',
        ]);

        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        $response = $this->getJson("/api/suppliers/{$supplier->id}/export");

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Nordic Structures Inc.')
            ->assertJsonPath('data.layups.0.name', 'CLT-5-150-L')
            ->assertJsonPath('data.layups.0.layers.0.layer_order', 1);
    }

    public function test_it_rejects_entire_import_when_conflicts_exist_and_strategy_is_reject(): void
    {
        $supplier = Supplier::create([
            'name'   => 'Nordic Structures Inc.',
            'status' => 'Active',
        ]);

        $layup = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-5-150-L',
            'ply_count'   => 5,
            'status'      => 'Active',
        ]);

        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 2,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 90,
        ]);

        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", [
            'strategy' => 'reject',
            'payload'  => [
                'layups' => [
                    [
                        'name'   => 'CLT-5-150-L',
                        'layers' => [
                            [
                                'layer_order' => 2,
                                'thickness'   => 35,
                                'width'       => 1200,
                                'angle'       => 90,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response
            ->assertStatus(409)
            ->assertJsonPath('status', 'conflict')
            ->assertJsonPath('data.conflicts.0.layup_name', 'CLT-5-150-L')
            ->assertJsonPath('data.conflicts.0.layer_order', 2);

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 2,
            'thickness'   => 30.00,
        ]);
    }

    public function test_it_skips_conflicting_layers_by_default_during_import(): void
    {
        $supplier = Supplier::create([
            'name'   => 'Nordic Structures Inc.',
            'status' => 'Active',
        ]);

        $layup = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-5-150-L',
            'ply_count'   => 5,
            'status'      => 'Active',
        ]);

        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 2,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 90,
        ]);

        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", [
            'payload' => [
                'layups' => [
                    [
                        'name'   => 'CLT-5-150-L',
                        'layers' => [
                            [
                                'layer_order' => 2,
                                'thickness'   => 35,
                                'width'       => 1200,
                                'angle'       => 90,
                            ],
                            [
                                'layer_order' => 3,
                                'thickness'   => 30,
                                'width'       => 1200,
                                'angle'       => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.summary.skipped_layers', 1)
            ->assertJsonPath('data.summary.created_layers', 1);

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 2,
            'thickness'   => 30.00,
        ]);

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 3,
            'thickness'   => 30.00,
        ]);
    }

    public function test_it_overwrites_conflicting_layers_when_strategy_is_overwrite(): void
    {
        $supplier = Supplier::create([
            'name'   => 'Nordic Structures Inc.',
            'status' => 'Active',
        ]);

        $layup = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-5-150-L',
            'ply_count'   => 5,
            'status'      => 'Active',
        ]);

        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", [
            'strategy' => 'overwrite',
            'payload'  => [
                'layups' => [
                    [
                        'name'   => 'CLT-5-150-L',
                        'layers' => [
                            [
                                'layer_order' => 1,
                                'thickness'   => 45,
                                'width'       => 1200,
                                'angle'       => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.summary.updated_layers', 1);

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 45.00,
        ]);
    }

    public function test_dry_run_returns_preview_without_persisting(): void
    {
        $supplier = Supplier::create([
            'name'   => 'Nordic Structures Inc.',
            'status' => 'Active',
        ]);

        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", [
            'dry_run' => true,
            'payload' => [
                'layups' => [
                    [
                        'name'   => 'NEW-LAYUP',
                        'layers' => [
                            ['layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0],
                        ],
                    ],
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.dry_run', true)
            ->assertJsonPath('data.summary.created_layups', 1)
            ->assertJsonPath('data.summary.created_layers', 1);

        $this->assertDatabaseMissing('clt_layups', ['name' => 'NEW-LAYUP']);
    }
}
