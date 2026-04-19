<?php

namespace Database\Seeders;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code'                    => 'SUP-TOR-001',
                'name'                    => 'Toray Composites',
                'primary_contact'         => 'john.smith@toraycomposites.com',
                'location'                => 'Tacoma, WA, USA',
                'material_certifications' => 'PEFC, FSC, EN 16351',
                'last_audit_date'         => '2024-03-15',
                'status'                  => 'Active',
            ],
            [
                'code'                    => 'SUP-HEX-002',
                'name'                    => 'Hexcel Corporation',
                'primary_contact'         => 'sarah.johnson@hexcel.com',
                'location'                => 'Stamford, CT, USA',
                'material_certifications' => 'FSC, EN 16351, ETA-14/0349',
                'last_audit_date'         => '2024-06-20',
                'status'                  => 'Active',
            ],
            [
                'code'                    => 'SUP-SOL-003',
                'name'                    => 'Solvay Composite Materials',
                'primary_contact'         => 'marc.dupont@solvay.com',
                'location'                => 'Brussels, Belgium',
                'material_certifications' => 'PEFC, ISO 14001',
                'last_audit_date'         => '2023-11-10',
                'status'                  => 'Inactive',
            ],
            [
                'code'                    => 'SUP-HPA-004',
                'name'                    => 'Hexa Paragon',
                'primary_contact'         => 'contact@hexaparagon.co.nz',
                'location'                => 'Rotorua, New Zealand',
                'material_certifications' => 'FSC, PEFC, EN 16351',
                'last_audit_date'         => '2025-01-22',
                'status'                  => 'Draft',
            ],
        ];

        $layupTemplates = [
            'Toray Composites' => [
                [
                    'specification_code' => 'CLT-TOR-3L-90',
                    'ply_count'          => 3,
                    'grade'              => 'C24',
                    'status'             => 'Active',
                    'description'        => 'Standard 3-ply CLT panel, 90mm, residential walls',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 30.00, 'width' => 120.00, 'angle' => 0,  'grade' => 'C24'],
                        ['layer_order' => 2, 'thickness' => 30.00, 'width' => 120.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 30.00, 'width' => 120.00, 'angle' => 0,  'grade' => 'C24'],
                    ],
                ],
                [
                    'specification_code' => 'CLT-TOR-5L-160',
                    'ply_count'          => 5,
                    'grade'              => 'C30',
                    'status'             => 'Active',
                    'description'        => '5-ply CLT floor panel, 160mm',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 40.00, 'width' => 150.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 2, 'thickness' => 20.00, 'width' => 150.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 40.00, 'width' => 150.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 4, 'thickness' => 20.00, 'width' => 150.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 5, 'thickness' => 40.00, 'width' => 150.00, 'angle' => 0,  'grade' => 'C30'],
                    ],
                ],
            ],
            'Hexcel Corporation' => [
                [
                    'specification_code' => 'CLT-HEX-3L-100',
                    'ply_count'          => 3,
                    'grade'              => 'C24',
                    'status'             => 'Active',
                    'description'        => '3-ply wall element, 100mm',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 35.00, 'width' => 125.00, 'angle' => 0,  'grade' => 'C24'],
                        ['layer_order' => 2, 'thickness' => 30.00, 'width' => 125.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 35.00, 'width' => 125.00, 'angle' => 0,  'grade' => 'C24'],
                    ],
                ],
                [
                    'specification_code' => 'CLT-HEX-7L-240',
                    'ply_count'          => 7,
                    'grade'              => 'C30',
                    'status'             => 'Draft',
                    'description'        => '7-ply heavy-load floor panel, 240mm',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 40.00, 'width' => 180.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 2, 'thickness' => 30.00, 'width' => 180.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 40.00, 'width' => 180.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 4, 'thickness' => 20.00, 'width' => 180.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 5, 'thickness' => 40.00, 'width' => 180.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 6, 'thickness' => 30.00, 'width' => 180.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 7, 'thickness' => 40.00, 'width' => 180.00, 'angle' => 0,  'grade' => 'C30'],
                    ],
                ],
            ],
            'Solvay Composite Materials' => [
                [
                    'specification_code' => 'CLT-SOL-5L-140',
                    'ply_count'          => 5,
                    'grade'              => 'C24',
                    'status'             => 'Archived',
                    'description'        => 'Legacy 5-ply spec, 140mm',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 30.00, 'width' => 100.00, 'angle' => 0,  'grade' => 'C24'],
                        ['layer_order' => 2, 'thickness' => 20.00, 'width' => 100.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 40.00, 'width' => 100.00, 'angle' => 0,  'grade' => 'C24'],
                        ['layer_order' => 4, 'thickness' => 20.00, 'width' => 100.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 5, 'thickness' => 30.00, 'width' => 100.00, 'angle' => 0,  'grade' => 'C24'],
                    ],
                ],
            ],
            'Hexa Paragon' => [
                [
                    'specification_code' => 'CLT-HPA-3L-90',
                    'ply_count'          => 3,
                    'grade'              => 'C24',
                    'status'             => 'Draft',
                    'description'        => 'Radiata pine 3-ply wall panel, 90mm',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 30.00, 'width' => 120.00, 'angle' => 0,  'grade' => 'C24'],
                        ['layer_order' => 2, 'thickness' => 30.00, 'width' => 120.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 30.00, 'width' => 120.00, 'angle' => 0,  'grade' => 'C24'],
                    ],
                ],
                [
                    'specification_code' => 'CLT-HPA-5L-175',
                    'ply_count'          => 5,
                    'grade'              => 'C30',
                    'status'             => 'Active',
                    'description'        => '5-ply floor panel, 175mm',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 45.00, 'width' => 160.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 2, 'thickness' => 20.00, 'width' => 160.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 3, 'thickness' => 45.00, 'width' => 160.00, 'angle' => 0,  'grade' => 'C30'],
                        ['layer_order' => 4, 'thickness' => 20.00, 'width' => 160.00, 'angle' => 90, 'grade' => 'C24'],
                        ['layer_order' => 5, 'thickness' => 45.00, 'width' => 160.00, 'angle' => 0,  'grade' => 'C30'],
                    ],
                ],
            ],
        ];

        foreach ($suppliers as $supplierData) {
            $supplier = Supplier::updateOrCreate(
                ['code' => $supplierData['code']],
                $supplierData,
            );

            $layups = $layupTemplates[$supplier->name] ?? [];

            foreach ($layups as $layupData) {
                $layers = $layupData['layers'];
                unset($layupData['layers']);

                // Derive a deterministic display name from spec code so re-runs are idempotent.
                $layupData['name'] = $layupData['specification_code'];

                // Delete + recreate layup (+ cascade layers) for clean re-seed.
                $supplier->layups()->where('name', $layupData['name'])->delete();
                $layup = $supplier->layups()->create($layupData);

                foreach ($layers as $layerData) {
                    $layup->layers()->create($layerData);
                }
            }
        }
    }
}
