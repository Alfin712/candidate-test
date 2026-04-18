<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SupplierExportServiceInterface;
use App\Contracts\SupplierImportServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponsable;
use App\Http\Requests\SupplierImportRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierTransferController extends Controller
{
    use ApiResponsable;

    public function __construct(
        private readonly SupplierExportServiceInterface $exportService,
        private readonly SupplierImportServiceInterface $importService,
    ) {
    }

    public function export(Supplier $supplier): JsonResponse
    {
        return $this->success(
            $this->exportService->export($supplier),
            'Export successful'
        );
    }

    public function import(SupplierImportRequest $request, Supplier $supplier): JsonResponse
    {
        $result = $this->importService->import(
            supplier: $supplier,
            payload: $request->validated('payload'),
            strategy: $request->validated('strategy', 'skip'),
            dryRun: (bool) $request->validated('dry_run', false),
            resolutions: $request->validated('resolutions', []),
        );

        if ($result['status'] === 'conflict') {
            return $this->conflict($result, 'Conflicts detected — review and resolve');
        }

        $message = $result['dry_run'] ?? false
            ? 'Dry-run complete — no changes applied'
            : 'Import applied successfully';

        return $this->success($result, $message);
    }
}
