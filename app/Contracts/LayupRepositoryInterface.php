<?php

namespace App\Contracts;

use App\Models\Layup;
use App\Models\Supplier;

interface LayupRepositoryInterface
{
    public function createFor(Supplier $supplier, array $data): Layup;

    public function update(Layup $layup, array $data): Layup;

    public function delete(Layup $layup): void;

    public function findWithLayers(Layup $layup): Layup;
}
