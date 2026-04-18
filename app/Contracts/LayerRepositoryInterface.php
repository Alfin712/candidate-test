<?php

namespace App\Contracts;

use App\Models\Layer;
use App\Models\Layup;

interface LayerRepositoryInterface
{
    public function createFor(Layup $layup, array $data): Layer;

    public function update(Layer $layer, array $data): Layer;

    public function delete(Layer $layer): void;
}
