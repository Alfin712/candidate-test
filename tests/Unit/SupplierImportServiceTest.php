<?php

namespace Tests\Unit;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Services\SupplierImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private SupplierImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SupplierImportService();
    }

    public function test_creates_new_layup_and_layers_when_nothing_exists(): void
    {
        $supplier = Supplier::create(['name' => 'Test Supplier', 'status' => 'Active']);

        $result = $this->service->import(
            supplier: $supplier,
            payload: [
                'layups' => [[
                    'name'   => 'NEW-LAYUP',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 10, 'width' => 100, 'angle' => 0],
                        ['layer_order' => 2, 'thickness' => 10, 'width' => 100, 'angle' => 45],
                    ],
                ]],
            ],
            strategy: 'skip',
        );

        $this->assertSame('ok', $result['status']);
        $this->assertSame(1, $result['summary']['created_layups']);
        $this->assertSame(2, $result['summary']['created_layers']);
    }

    public function test_reject_strategy_returns_conflicts_without_persisting(): void
    {
        [$supplier, $layup] = $this->seedSupplierWithLayer();

        $result = $this->service->import(
            supplier: $supplier,
            payload: [
                'layups' => [[
                    'name'   => $layup->name,
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 100, 'angle' => 0],
                    ],
                ]],
            ],
            strategy: 'reject',
        );

        $this->assertSame('conflict', $result['status']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'thickness' => 10.00]);
    }

    public function test_overwrite_strategy_updates_conflicting_layer(): void
    {
        [$supplier, $layup] = $this->seedSupplierWithLayer();

        $this->service->import(
            supplier: $supplier,
            payload: [
                'layups' => [[
                    'name'   => $layup->name,
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 100, 'angle' => 0],
                    ],
                ]],
            ],
            strategy: 'overwrite',
        );

        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'thickness' => 99.00]);
    }

    public function test_dry_run_does_not_persist(): void
    {
        $supplier = Supplier::create(['name' => 'DryRun Supplier', 'status' => 'Active']);

        $result = $this->service->import(
            supplier: $supplier,
            payload: [
                'layups' => [[
                    'name'   => 'X',
                    'layers' => [['layer_order' => 1, 'thickness' => 1, 'width' => 1, 'angle' => 0]],
                ]],
            ],
            strategy: 'skip',
            dryRun: true,
        );

        $this->assertTrue($result['dry_run']);
        $this->assertDatabaseMissing('clt_layups', ['name' => 'X']);
    }

    public function test_manual_resolution_keep_existing_overrides_global_strategy(): void
    {
        [$supplier, $layup] = $this->seedSupplierWithLayer();

        $this->service->import(
            supplier: $supplier,
            payload: [
                'layups' => [[
                    'name'   => $layup->name,
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 100, 'angle' => 0],
                    ],
                ]],
            ],
            strategy: 'overwrite',
            resolutions: [
                ['layup_name' => $layup->name, 'layer_order' => 1, 'action' => 'keep_existing'],
            ],
        );

        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'thickness' => 10.00]);
    }

    public function test_duplicate_strategy_creates_new_layup_with_suffix(): void
    {
        [$supplier, $layup] = $this->seedSupplierWithLayer();

        $result = $this->service->import(
            supplier: $supplier,
            payload: [
                'layups' => [[
                    'name'   => $layup->name,
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 100, 'angle' => 0],
                    ],
                ]],
            ],
            strategy: 'duplicate',
        );

        $this->assertSame(1, $result['summary']['duplicated_layups']);
        $this->assertDatabaseHas('clt_layups', ['name' => $layup->name.' (imported)']);
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'thickness' => 10.00]);
    }

    public function test_idempotent_same_payload_twice_no_duplication(): void
    {
        $supplier = Supplier::create(['name' => 'Idem Supplier', 'status' => 'Active']);
        $payload = [
            'layups' => [[
                'name'   => 'IDEM',
                'layers' => [['layer_order' => 1, 'thickness' => 5, 'width' => 50, 'angle' => 0]],
            ]],
        ];

        $this->service->import(supplier: $supplier, payload: $payload, strategy: 'skip');
        $this->service->import(supplier: $supplier, payload: $payload, strategy: 'skip');

        $this->assertSame(1, Layup::where('supplier_id', $supplier->id)->count());
        $this->assertSame(1, Layer::whereIn('layup_id', $supplier->layups->pluck('id'))->count());
    }

    private function seedSupplierWithLayer(): array
    {
        $supplier = Supplier::create(['name' => 'Seeded Supplier', 'status' => 'Active']);
        $layup = Layup::create([
            'supplier_id' => $supplier->id,
            'name'        => 'LU-1',
            'status'      => 'Active',
        ]);
        Layer::create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 10,
            'width'       => 100,
            'angle'       => 0,
        ]);

        return [$supplier, $layup];
    }
}
