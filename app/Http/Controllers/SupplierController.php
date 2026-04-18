<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()->withCount('layups');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('manager.suppliers.index', compact('suppliers', 'search', 'status'));
    }

    public function create()
    {
        return view('manager.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $supplier = Supplier::create($data);
        return redirect()->route('suppliers.show', $supplier)->with('status', 'Supplier created.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->loadCount('layups');
        $layups = $supplier->layups()->withCount('layers')->orderBy('name')->get();
        return view('manager.suppliers.show', compact('supplier', 'layups'));
    }

    public function edit(Supplier $supplier)
    {
        return view('manager.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $this->validateData($request, $supplier->id);
        $supplier->update($data);
        return redirect()->route('suppliers.show', $supplier)->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('status', 'Supplier deleted.');
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['nullable', 'string', 'max:64', Rule::unique('suppliers', 'code')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($ignoreId)],
            'primary_contact' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'material_certifications' => ['nullable', 'string'],
            'last_audit_date' => ['nullable', 'date'],
            'status' => ['required', 'in:Active,Inactive,Draft'],
        ]);
    }
}
