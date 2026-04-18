<?php

namespace App\Services;

use App\Contracts\SupplierExportServiceInterface;
use App\Models\Supplier;

class SupplierExportService implements SupplierExportServiceInterface
{
    public function export(Supplier $supplier): array
    {
        $supplier->load([
            'layups.layers',
        ]);

        return [
            'id'   => $supplier->id,
            'code' => $supplier->code,
            'name' => $supplier->name,
            'primary_contact' => $supplier->primary_contact,
            'location' => $supplier->location,
            'material_certifications' => $supplier->material_certifications,
            'last_audit_date' => optional($supplier->last_audit_date)?->toDateString(),
            'status' => $supplier->status,
            'layups' => $supplier->layups->map(function ($layup) {
                return [
                    'id'                 => $layup->id,
                    'name'               => $layup->name,
                    'description'        => $layup->description,
                    'specification_code' => $layup->specification_code,
                    'ply_count' => $layup->ply_count,
                    'grade' => $layup->grade,
                    'status' => $layup->status,
                    'layers' => $layup->layers->map(function ($layer) {
                        return [
                            'id' => $layer->id,
                            'layer_order' => $layer->layer_order,
                            'thickness' => (float) $layer->thickness,
                            'width' => (float) $layer->width,
                            'angle' => (float) $layer->angle,
                            'grade' => $layer->grade,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }
}

