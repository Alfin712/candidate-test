@props(['supplier' => null])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
        <input type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Primary Contact</label>
        <input type="text" name="primary_contact" value="{{ old('primary_contact', $supplier->primary_contact ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
        <input type="text" name="location" value="{{ old('location', $supplier->location ?? '') }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Material Certifications</label>
        <textarea name="material_certifications" rows="2"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">{{ old('material_certifications', $supplier->material_certifications ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Last Audit Date</label>
        <input type="date" name="last_audit_date" value="{{ old('last_audit_date', optional($supplier->last_audit_date ?? null)->format('Y-m-d')) }}"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            @foreach (['Active', 'Inactive', 'Draft'] as $s)
                <option value="{{ $s }}" @selected(old('status', $supplier->status ?? 'Active') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
</div>
