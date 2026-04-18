<?php

namespace App\Contracts;

use App\Models\Supplier;

interface SupplierImportServiceInterface
{
    public function import(Supplier $supplier, array $payload, string $strategy = 'skip', bool $dryRun = false, array $resolutions = []): array;
}

