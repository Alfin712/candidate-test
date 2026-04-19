<?php

namespace App\Http\Controllers;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayupController extends Controller
{
    public function index(Request $request)
    {
        $query = Layup::with('supplier')->withCount('layers');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specification_code', 'like', "%{$search}%")
                  ->orWhere('grade', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($supplierId = $request->query('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        $layups = $query->orderBy('name')->paginate(12)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('manager.layups.index', compact('layups', 'suppliers', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $selectedSupplierId = $request->query('supplier_id');
        return view('manager.layups.create', compact('suppliers', 'selectedSupplierId'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $layup = Layup::create($data);
        return redirect()->route('layups.show', $layup)->with('success', 'Layup created.');
    }

    public function show(Layup $layup)
    {
        $layup->load(['supplier', 'layers']);
        return view('manager.layups.show', compact('layup'));
    }

    public function edit(Layup $layup)
    {
        $layup->load('layers');
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        return view('manager.layups.edit', compact('layup', 'suppliers'));
    }

    public function update(Request $request, Layup $layup)
    {
        $data = $this->validateData($request, $layup);
        $layup->update($data);
        return redirect()->route('layups.show', $layup)->with('success', 'Layup updated.');
    }

    public function destroy(Layup $layup)
    {
        $supplierId = $layup->supplier_id;
        $layup->delete();
        return redirect()->route('suppliers.show', $supplierId)->with('success', 'Layup deleted.');
    }

    protected function validateData(Request $request, ?Layup $layup = null): array
    {
        $supplierId = $request->input('supplier_id');
        $uniqueRule = Rule::unique('clt_layups', 'name')
            ->where(fn ($q) => $q->where('supplier_id', $supplierId));
        if ($layup) {
            $uniqueRule = $uniqueRule->ignore($layup->id);
        }

        return $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'description' => ['nullable', 'string'],
            'specification_code' => ['nullable', 'string', 'max:255'],
            'ply_count' => ['nullable', 'integer', 'min:0'],
            'grade' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Draft,Archived'],
        ]);
    }
}
