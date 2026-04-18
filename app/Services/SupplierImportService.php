<?php

namespace App\Services;

use App\Contracts\SupplierImportServiceInterface;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierImportService implements SupplierImportServiceInterface
{
    public function import(
        Supplier $supplier,
        array $payload,
        string $strategy = 'skip',
        bool $dryRun = false,
        array $resolutions = []
    ): array {
        $normalizedPayload = $this->normalizePayload($payload);
        $resolutionMap = $this->makeResolutionMap($resolutions);

        $conflicts = $this->detectConflicts($supplier, $normalizedPayload);

        if ($strategy === 'reject' && ! empty($conflicts)) {
            return [
                'status' => 'conflict',
                'dry_run' => $dryRun,
                'strategy' => $strategy,
                'conflicts' => $conflicts,
                'summary' => [
                    'created_layups'    => 0,
                    'updated_layups'    => 0,
                    'duplicated_layups' => 0,
                    'created_layers'    => 0,
                    'updated_layers'    => 0,
                    'skipped_layers'    => 0,
                ],
            ];
        }

        $summary = [
            'created_layups'    => 0,
            'updated_layups'    => 0,
            'duplicated_layups' => 0,
            'created_layers'    => 0,
            'updated_layers'    => 0,
            'skipped_layers'    => 0,
        ];

        if ($dryRun) {
            $summary = $this->simulateSummary($supplier, $normalizedPayload, $strategy, $resolutionMap);

            return [
                'status' => empty($conflicts) ? 'ok' : 'preview',
                'dry_run' => true,
                'strategy' => $strategy,
                'conflicts' => $conflicts,
                'summary' => $summary,
            ];
        }

        DB::transaction(function () use ($supplier, $normalizedPayload, $strategy, $resolutionMap, &$summary) {
            $supplier->fill(array_filter([
                'name' => $normalizedPayload['name'] ?: $supplier->name,
                'primary_contact' => $normalizedPayload['primary_contact'],
                'location' => $normalizedPayload['location'],
                'material_certifications' => $normalizedPayload['material_certifications'],
                'last_audit_date' => $normalizedPayload['last_audit_date'],
                'status' => $normalizedPayload['status'] ?: $supplier->status,
            ], static fn ($value) => $value !== null));

            if ($supplier->isDirty()) {
                $supplier->save();
            }

            foreach ($normalizedPayload['layups'] as $incomingLayup) {
                $existingLayup = $supplier->layups()->where('name', $incomingLayup['name'])->first();

                if (! $existingLayup) {
                    $existingLayup = $supplier->layups()->create([
                        'name'               => $incomingLayup['name'],
                        'description'        => $incomingLayup['description'] ?? null,
                        'specification_code' => $incomingLayup['specification_code'],
                        'ply_count'          => $incomingLayup['ply_count'],
                        'grade'              => $incomingLayup['grade'],
                        'status'             => $incomingLayup['status'],
                    ]);

                    $summary['created_layups']++;

                    foreach ($incomingLayup['layers'] as $incomingLayer) {
                        $existingLayup->layers()->create($incomingLayer);
                        $summary['created_layers']++;
                    }

                    continue;
                }

                if ($strategy === 'duplicate' && $this->layupHasAnyConflict($existingLayup, $incomingLayup)) {
                    $duplicateName = $this->nextDuplicateName($supplier, $incomingLayup['name']);
                    $duplicated = $supplier->layups()->create([
                        'name'               => $duplicateName,
                        'description'        => $incomingLayup['description'] ?? null,
                        'specification_code' => $incomingLayup['specification_code'],
                        'ply_count'          => $incomingLayup['ply_count'],
                        'grade'              => $incomingLayup['grade'],
                        'status'             => $incomingLayup['status'],
                    ]);
                    $summary['created_layups']++;
                    $summary['duplicated_layups']++;

                    foreach ($incomingLayup['layers'] as $incomingLayer) {
                        $duplicated->layers()->create($incomingLayer);
                        $summary['created_layers']++;
                    }
                    continue;
                }

                $existingLayup->fill(array_filter([
                    'description'        => $incomingLayup['description'] ?? null,
                    'specification_code' => $incomingLayup['specification_code'],
                    'ply_count'          => $incomingLayup['ply_count'],
                    'grade'              => $incomingLayup['grade'],
                    'status'             => $incomingLayup['status'],
                ], static fn ($v) => $v !== null));

                if ($existingLayup->isDirty()) {
                    $existingLayup->save();
                    $summary['updated_layups']++;
                }

                foreach ($incomingLayup['layers'] as $incomingLayer) {
                    $existingLayer = $existingLayup->layers()
                        ->where('layer_order', $incomingLayer['layer_order'])
                        ->first();

                    if (! $existingLayer) {
                        $existingLayup->layers()->create($incomingLayer);
                        $summary['created_layers']++;
                        continue;
                    }

                    if (! $this->layerHasConflict($existingLayer, $incomingLayer)) {
                        continue;
                    }

                    $decision = $this->resolveAction(
                        $strategy,
                        $resolutionMap,
                        $incomingLayup['name'],
                        $incomingLayer['layer_order']
                    );

                    if ($decision === 'keep_existing') {
                        $summary['skipped_layers']++;
                        continue;
                    }

                    $existingLayer->update($incomingLayer);
                    $summary['updated_layers']++;
                }
            }
        });

        return [
            'status' => empty($conflicts) ? 'ok' : 'completed_with_conflicts',
            'dry_run' => false,
            'strategy' => $strategy,
            'conflicts' => $conflicts,
            'summary' => $summary,
        ];
    }

    private function normalizePayload(array $payload): array
    {
        $layups = collect($payload['layups'] ?? [])
            ->map(function (array $layup) {
                return [
                    'name'               => trim($layup['name']),
                    'description'        => $layup['description'] ?? null,
                    'specification_code' => $layup['specification_code'] ?? null,
                    'ply_count'          => $layup['ply_count'] ?? null,
                    'grade'              => $layup['grade'] ?? null,
                    'status'             => $layup['status'] ?? 'Active',
                    'layers' => collect($layup['layers'] ?? [])
                        ->map(function (array $layer) {
                            return [
                                'layer_order' => (int) $layer['layer_order'],
                                'thickness' => (float) $layer['thickness'],
                                'width' => (float) $layer['width'],
                                'angle' => (float) $layer['angle'],
                                'grade' => $layer['grade'] ?? null,
                            ];
                        })
                        ->sortBy('layer_order')
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        return [
            'name' => $payload['name'] ?? null,
            'primary_contact' => $payload['primary_contact'] ?? null,
            'location' => $payload['location'] ?? null,
            'material_certifications' => $payload['material_certifications'] ?? null,
            'last_audit_date' => $payload['last_audit_date'] ?? null,
            'status' => $payload['status'] ?? null,
            'layups' => $layups,
        ];
    }

    private function detectConflicts(Supplier $supplier, array $payload): array
    {
        $supplier->load('layups.layers');

        $conflicts = [];

        foreach ($payload['layups'] as $incomingLayup) {
            /** @var Layup|null $existingLayup */
            $existingLayup = $supplier->layups->firstWhere('name', $incomingLayup['name']);

            if (! $existingLayup) {
                continue;
            }

            foreach ($incomingLayup['layers'] as $incomingLayer) {
                /** @var Layer|null $existingLayer */
                $existingLayer = $existingLayup->layers->firstWhere('layer_order', $incomingLayer['layer_order']);

                if (! $existingLayer || ! $this->layerHasConflict($existingLayer, $incomingLayer)) {
                    continue;
                }

                $conflicts[] = [
                    'layup_name' => $incomingLayup['name'],
                    'layer_order' => $incomingLayer['layer_order'],
                    'existing' => [
                        'thickness' => (float) $existingLayer->thickness,
                        'width' => (float) $existingLayer->width,
                        'angle' => (float) $existingLayer->angle,
                        'grade' => $existingLayer->grade,
                    ],
                    'incoming' => $incomingLayer,
                ];
            }
        }

        return $conflicts;
    }

    private function layerHasConflict(Layer $existingLayer, array $incomingLayer): bool
    {
        return (float) $existingLayer->thickness !== (float) $incomingLayer['thickness']
            || (float) $existingLayer->width !== (float) $incomingLayer['width']
            || (float) $existingLayer->angle !== (float) $incomingLayer['angle'];
    }

    private function makeResolutionMap(array $resolutions): array
    {
        $map = [];

        foreach ($resolutions as $resolution) {
            $key = $this->resolutionKey($resolution['layup_name'], (int) $resolution['layer_order']);
            $map[$key] = $resolution['action'];
        }

        return $map;
    }

    private function resolveAction(string $strategy, array $resolutionMap, string $layupName, int $layerOrder): string
    {
        $manualAction = $resolutionMap[$this->resolutionKey($layupName, $layerOrder)] ?? null;

        if ($manualAction) {
            return $manualAction;
        }

        return $strategy === 'overwrite' ? 'accept_incoming' : 'keep_existing';
    }

    private function resolutionKey(string $layupName, int $layerOrder): string
    {
        return mb_strtolower(trim($layupName)).'|'.$layerOrder;
    }

    private function layupHasAnyConflict(Layup $existingLayup, array $incomingLayup): bool
    {
        foreach ($incomingLayup['layers'] as $incomingLayer) {
            $existingLayer = $existingLayup->layers->firstWhere('layer_order', $incomingLayer['layer_order']);
            if ($existingLayer && $this->layerHasConflict($existingLayer, $incomingLayer)) {
                return true;
            }
        }

        return false;
    }

    private function nextDuplicateName(Supplier $supplier, string $baseName): string
    {
        $candidate = $baseName.' (imported)';
        $counter = 2;
        while ($supplier->layups()->where('name', $candidate)->exists()) {
            $candidate = $baseName.' (imported '.$counter.')';
            $counter++;
        }

        return $candidate;
    }

    private function simulateSummary(Supplier $supplier, array $payload, string $strategy, array $resolutionMap): array
    {
        $supplier->load('layups.layers');

        $summary = [
            'created_layups'    => 0,
            'updated_layups'    => 0,
            'duplicated_layups' => 0,
            'created_layers'    => 0,
            'updated_layers'    => 0,
            'skipped_layers'    => 0,
        ];

        foreach ($payload['layups'] as $incomingLayup) {
            /** @var Layup|null $existingLayup */
            $existingLayup = $supplier->layups->firstWhere('name', $incomingLayup['name']);

            if (! $existingLayup) {
                $summary['created_layups']++;
                $summary['created_layers'] += count($incomingLayup['layers']);
                continue;
            }

            if ($strategy === 'duplicate' && $this->layupHasAnyConflict($existingLayup, $incomingLayup)) {
                $summary['created_layups']++;
                $summary['duplicated_layups']++;
                $summary['created_layers'] += count($incomingLayup['layers']);
                continue;
            }

            foreach ($incomingLayup['layers'] as $incomingLayer) {
                /** @var Layer|null $existingLayer */
                $existingLayer = $existingLayup->layers->firstWhere('layer_order', $incomingLayer['layer_order']);

                if (! $existingLayer) {
                    $summary['created_layers']++;
                    continue;
                }

                if (! $this->layerHasConflict($existingLayer, $incomingLayer)) {
                    continue;
                }

                $decision = $this->resolveAction(
                    $strategy,
                    $resolutionMap,
                    $incomingLayup['name'],
                    $incomingLayer['layer_order']
                );

                if ($decision === 'keep_existing') {
                    $summary['skipped_layers']++;
                } else {
                    $summary['updated_layers']++;
                }
            }
        }

        return $summary;
    }
}

