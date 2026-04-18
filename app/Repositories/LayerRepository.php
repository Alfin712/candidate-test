<?php

namespace App\Repositories;

use App\Contracts\LayerRepositoryInterface;
use App\Models\Layer;
use App\Models\Layup;

class LayerRepository implements LayerRepositoryInterface
{
    public function createFor(Layup $layup, array $data): Layer
    {
        return $layup->layers()->create($data);
    }

    public function update(Layer $layer, array $data): Layer
    {
        $layer->update($data);

        return $layer->fresh();
    }

    public function delete(Layer $layer): void
    {
        $layer->delete();
    }
}
