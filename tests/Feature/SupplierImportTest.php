<?php

namespace Tests\Feature;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierImportTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staff  = User::factory()->create(['role' => User::ROLE_STAFF]);
        $this->viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);
    }

    // -----------------------------------------------------------------------
    // Happy path: new layup is created successfully
    // -----------------------------------------------------------------------

    public function test_staff_imports_new_layup_and_returns_200(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();

        $payload = [
            'layups' => [
                [
                    'name'   => 'NEW-LAYUP-001',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 20, 'width' => 600, 'angle' => 0],
                    ],
                ],
            ],
        ];

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'payload' => $payload,
            ]);

        // Assert
        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('clt_layups', ['supplier_id' => $supplier->id, 'name' => 'NEW-LAYUP-001']);
    }

    // -----------------------------------------------------------------------
    // Dry run: nothing persisted
    // -----------------------------------------------------------------------

    public function test_dry_run_does_not_persist_data(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'dry_run' => true,
                'payload' => [
                    'layups' => [
                        [
                            'name'   => 'DRYRUN-LAYUP',
                            'layers' => [
                                ['layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0],
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert
        $response->assertOk()
            ->assertJsonPath('data.dry_run', true);

        $this->assertDatabaseMissing('clt_layups', ['name' => 'DRYRUN-LAYUP']);
    }

    // -----------------------------------------------------------------------
    // Conflict on differing layer values → 409 with conflicts[]
    // -----------------------------------------------------------------------

    public function test_conflict_on_differing_layer_returns_409_with_conflict_details(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();
        $layup    = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-CONFLICT',
            'ply_count'   => 3,
            'status'      => 'Active',
        ]);
        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy' => 'reject',
                'payload'  => [
                    'layups' => [
                        [
                            'name'   => 'CLT-CONFLICT',
                            'layers' => [
                                ['layer_order' => 1, 'thickness' => 99, 'width' => 1200, 'angle' => 0],
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert
        $response->assertStatus(409)
            ->assertJsonPath('status', 'conflict')
            ->assertJsonPath('data.conflicts.0.layup_name', 'CLT-CONFLICT')
            ->assertJsonPath('data.conflicts.0.layer_order', 1);

        // existing value must be preserved (not overwritten)
        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => '30.00',
        ]);
    }

    // -----------------------------------------------------------------------
    // Strategy=skip keeps existing, ignores incoming changes
    // -----------------------------------------------------------------------

    public function test_strategy_skip_keeps_existing_layer_unchanged(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();
        $layup    = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-SKIP',
            'ply_count'   => 2,
            'status'      => 'Active',
        ]);
        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy' => 'skip',
                'payload'  => [
                    'layups' => [
                        [
                            'name'   => 'CLT-SKIP',
                            'layers' => [
                                ['layer_order' => 1, 'thickness' => 99, 'width' => 1200, 'angle' => 0],
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert: success (skip strategy never 409)
        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.summary.skipped_layers', 1);

        // existing value unchanged
        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => '30.00',
        ]);
    }

    // -----------------------------------------------------------------------
    // Strategy=overwrite replaces existing layers
    // -----------------------------------------------------------------------

    public function test_strategy_overwrite_replaces_existing_layer(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();
        $layup    = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-OVERWRITE',
            'ply_count'   => 2,
            'status'      => 'Active',
        ]);
        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy' => 'overwrite',
                'payload'  => [
                    'layups' => [
                        [
                            'name'   => 'CLT-OVERWRITE',
                            'layers' => [
                                ['layer_order' => 1, 'thickness' => 55, 'width' => 1200, 'angle' => 0],
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert
        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.summary.updated_layers', 1);

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => '55.00',
        ]);
    }

    // -----------------------------------------------------------------------
    // Strategy=manual with resolutions: per-conflict choice applied
    // -----------------------------------------------------------------------

    public function test_strategy_manual_keep_existing_keeps_original_value(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();
        $layup    = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-MANUAL',
            'ply_count'   => 2,
            'status'      => 'Active',
        ]);
        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy'    => 'manual',
                'resolutions' => [
                    ['layup_name' => 'CLT-MANUAL', 'layer_order' => 1, 'action' => 'keep_existing'],
                ],
                'payload'  => [
                    'layups' => [
                        [
                            'name'   => 'CLT-MANUAL',
                            'layers' => [
                                ['layer_order' => 1, 'thickness' => 99, 'width' => 1200, 'angle' => 0],
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert: success + existing value unchanged
        $response->assertOk();

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => '30.00',
        ]);
    }

    public function test_strategy_manual_accept_incoming_replaces_value(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();
        $layup    = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'CLT-MANUAL-ACC',
            'ply_count'   => 2,
            'status'      => 'Active',
        ]);
        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 30,
            'width'       => 1200,
            'angle'       => 0,
        ]);

        // Act
        $response = $this->actingAs($this->staff)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'strategy'    => 'manual',
                'resolutions' => [
                    ['layup_name' => 'CLT-MANUAL-ACC', 'layer_order' => 1, 'action' => 'accept_incoming'],
                ],
                'payload'  => [
                    'layups' => [
                        [
                            'name'   => 'CLT-MANUAL-ACC',
                            'layers' => [
                                ['layer_order' => 1, 'thickness' => 77, 'width' => 1200, 'angle' => 0],
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert: incoming value written to DB
        $response->assertOk();

        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => '77.00',
        ]);
    }

    // -----------------------------------------------------------------------
    // Authorization: viewer cannot import
    // -----------------------------------------------------------------------

    public function test_viewer_cannot_import_returns_403(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();

        // Act
        $response = $this->actingAs($this->viewer)
            ->postJson("/api/suppliers/{$supplier->id}/import", [
                'payload' => ['layups' => []],
            ]);

        // Assert
        $response->assertStatus(403);
    }
}
