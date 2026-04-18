@props(['layup' => null, 'suppliers', 'selectedSupplierId' => null])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
        <select name="supplier_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="">Select supplier...</option>
            @foreach ($suppliers as $s)
                <option value="{{ $s->id }}" @selected(old('supplier_id', $layup->supplier_id ?? $selectedSupplierId) == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $layup->name ?? '') }}" required
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Specification Code</label>
        <input type="text" name="specification_code" value="{{ old('specification_code', $layup->specification_code ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ply Count</label>
        <input type="number" name="ply_count" min="0" value="{{ old('ply_count', $layup->ply_count ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
        <input type="text" name="grade" value="{{ old('grade', $layup->grade ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            @foreach (['Active', 'Draft', 'Archived'] as $s)
                <option value="{{ $s }}" @selected(old('status', $layup->status ?? 'Active') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Description / Engineering Note</label>
        <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">{{ old('description', $layup->description ?? '') }}</textarea>
    </div>
</div>
