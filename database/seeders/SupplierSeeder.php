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
                'name'                    => 'Toray Composites',
                'primary_contact'         => 'John Smith',
                'location'               => 'Tacoma, WA, USA',
                'material_certifications' => 'AS9100, NADCAP',
                'last_audit_date'         => '2024-03-15',
                'status'                  => 'active',
            ],
            [
                'name'                    => 'Hexcel Corporation',
                'primary_contact'         => 'Sarah Johnson',
                'location'               => 'Stamford, CT, USA',
                'material_certifications' => 'ISO 9001, AS9100',
                'last_audit_date'         => '2024-06-20',
                'status'                  => 'active',
            ],
            [
                'name'                    => 'Solvay Composite Materials',
                'primary_contact'         => 'Marc Dupont',
                'location'               => 'Brussels, Belgium',
                'material_certifications' => 'ISO 9001, ISO 14001',
                'last_audit_date'         => '2023-11-10',
                'status'                  => 'inactive',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            $supplier = Supplier::create($supplierData);

            $layupsData = [
                [
                    'name'        => 'LU-' . strtoupper(substr($supplier->name, 0, 3)) . '-001',
                    'description' => 'Standard quasi-isotropic layup',
                    'layers'      => [
                        ['layer_order' => 1, 'thickness' => 0.25, 'width' => 100.0, 'angle' => 0],
                        ['layer_order' => 2, 'thickness' => 0.25, 'width' => 100.0, 'angle' => 45],
                        ['layer_order' => 3, 'thickness' => 0.25, 'width' => 100.0, 'angle' => 90],
                        ['layer_order' => 4, 'thickness' => 0.25, 'width' => 100.0, 'angle' => -45],
                    ],
                ],
                [
                    'name'        => 'LU-' . strtoupper(substr($supplier->name, 0, 3)) . '-002',
                    'description' => 'Unidirectional high-load layup',
                    'layers'      => [
                        ['layer_order' => 1, 'thickness' => 0.50, 'width' => 150.0, 'angle' => 0],
                        ['layer_order' => 2, 'thickness' => 0.50, 'width' => 150.0, 'angle' => 0],
                        ['layer_order' => 3, 'thickness' => 0.25, 'width' => 150.0, 'angle' => 90],
                    ],
                ],
            ];

            foreach ($layupsData as $layupData) {
                $layers = $layupData['layers'];
                unset($layupData['layers']);

                $layup = $supplier->layups()->create($layupData);

                foreach ($layers as $layerData) {
                    $layup->layers()->create($layerData);
                }
            }
        }
    }
}
