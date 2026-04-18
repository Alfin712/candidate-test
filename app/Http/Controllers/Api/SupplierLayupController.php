<?php

namespace App\Http\Controllers\Api;

use App\Contracts\LayupRepositoryInterface;
use App\Http\Controllers\Api\Concerns\ApiResponsable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LayupStoreRequest;
use App\Http\Requests\LayupUpdateRequest;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierLayupController extends Controller
{
    use ApiResponsable;

    public function __construct(
        private readonly LayupRepositoryInterface $repository,
    ) {
    }

    public function store(LayupStoreRequest $request, Supplier $supplier): JsonResponse
    {
        return $this->created(
            $this->repository->createFor($supplier, $request->validated())
        );
    }

    public function show(Supplier $supplier, Layup $layup): JsonResponse
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);

        return $this->success(
            $this->repository->findWithLayers($layup)
        );
    }

    public function update(LayupUpdateRequest $request, Supplier $supplier, Layup $layup): JsonResponse
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);

        return $this->success(
            $this->repository->update($layup, $request->validated())
        );
    }

    public function destroy(Supplier $supplier, Layup $layup): JsonResponse
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);

        $this->repository->delete($layup);

        return $this->noContent();
    }
}
