<?php

namespace App\Repositories;

use App\Contracts\LayupRepositoryInterface;
use App\Models\Layup;
use App\Models\Supplier;

class LayupRepository implements LayupRepositoryInterface
{
    public function createFor(Supplier $supplier, array $data): Layup
    {
        return $supplier->layups()->create($data);
    }

    public function update(Layup $layup, array $data): Layup
    {
        $layup->update($data);

        return $layup->fresh();
    }

    public function delete(Layup $layup): void
    {
        $layup->delete();
    }

    public function findWithLayers(Layup $layup): Layup
    {
        return $layup->load('layers');
    }
}
