<?php

namespace App\Http\Controllers\Api;

use App\Contracts\LayerRepositoryInterface;
use App\Http\Controllers\Api\Concerns\ApiResponsable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LayerStoreRequest;
use App\Http\Requests\LayerUpdateRequest;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class LayupLayerController extends Controller
{
    use ApiResponsable;

    public function __construct(
        private readonly LayerRepositoryInterface $repository,
    ) {
    }

    public function store(LayerStoreRequest $request, Supplier $supplier, Layup $layup): JsonResponse
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);

        return $this->created(
            $this->repository->createFor($layup, $request->validated())
        );
    }

    public function update(LayerUpdateRequest $request, Supplier $supplier, Layup $layup, Layer $layer): JsonResponse
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);
        abort_unless($layer->layup_id === $layup->id, 404);

        return $this->success(
            $this->repository->update($layer, $request->validated())
        );
    }

    public function destroy(Supplier $supplier, Layup $layup, Layer $layer): JsonResponse
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);
        abort_unless($layer->layup_id === $layup->id, 404);

        $this->repository->delete($layer);

        return $this->noContent();
    }
}
