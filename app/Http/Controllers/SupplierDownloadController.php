<?php

namespace App\Http\Controllers;

use App\Contracts\SupplierExportServiceInterface;
use App\Models\Supplier;
use Illuminate\Http\Response;

class SupplierDownloadController extends Controller
{
    public function __construct(
        private readonly SupplierExportServiceInterface $exportService,
    ) {
    }

    /**
     * Force a supplier snapshot to download as a JSON file. Wraps the shared
     * export service so the /api endpoint behaviour remains untouched.
     */
    public function download(Supplier $supplier): Response
    {
        $data = $this->exportService->export($supplier);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $slug = $supplier->code ?: $supplier->id;
        $safeSlug = preg_replace('/[^A-Za-z0-9_\-]/', '-', (string) $slug) ?: (string) $supplier->id;
        $filename = sprintf('supplier-%s-%s.json', $safeSlug, now()->format('Ymd-His'));

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
