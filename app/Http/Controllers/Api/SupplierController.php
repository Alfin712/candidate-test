<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SupplierRepositoryInterface;
use App\Http\Controllers\Api\Concerns\ApiResponsable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ApiResponsable;

    public function __construct(
        private readonly SupplierRepositoryInterface $repository,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->repository->paginate(
            search: $request->query('q'),
            perPage: (int) $request->query('per_page', 15),
        );

        return $this->success($paginator);
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        return $this->created(
            $this->repository->create($request->validated())
        );
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return $this->success(
            $this->repository->findWithRelations($supplier->id)
        );
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): JsonResponse
    {
        return $this->success(
            $this->repository->update($supplier, $request->validated())
        );
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->repository->delete($supplier);

        return $this->noContent();
    }
}
