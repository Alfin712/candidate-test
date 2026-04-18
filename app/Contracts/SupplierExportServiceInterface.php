<?php

namespace App\Contracts;

use App\Models\Supplier;

interface SupplierExportServiceInterface
{
    public function export(Supplier $supplier): array;
}

