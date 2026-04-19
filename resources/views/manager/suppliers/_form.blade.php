@props(['supplier' => null])
@php
    $base = 'w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500';
    $err = 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500';
    $ok = 'border-gray-300';
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="f_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input id="f_name" type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required
               aria-invalid="@error('name') true @else false @enderror"
               @error('name') aria-describedby="f_name_err" @enderror
               class="{{ $base }} {{ $errors->has('name') ? $err : $ok }}">
        @error('name')<p id="f_name_err" class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
        <input id="f_code" type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}"
               class="{{ $base }} {{ $errors->has('code') ? $err : $ok }}">
        @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_primary_contact" class="block text-sm font-medium text-gray-700 mb-1">Primary Contact</label>
        <input id="f_primary_contact" type="text" name="primary_contact" value="{{ old('primary_contact', $supplier->primary_contact ?? '') }}"
               class="{{ $base }} {{ $errors->has('primary_contact') ? $err : $ok }}">
        @error('primary_contact')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
        <input id="f_location" type="text" name="location" value="{{ old('location', $supplier->location ?? '') }}"
               class="{{ $base }} {{ $errors->has('location') ? $err : $ok }}">
        @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="f_material_certifications" class="block text-sm font-medium text-gray-700 mb-1">Material Certifications</label>
        <textarea id="f_material_certifications" name="material_certifications" rows="2"
                  class="{{ $base }} {{ $errors->has('material_certifications') ? $err : $ok }}">{{ old('material_certifications', $supplier->material_certifications ?? '') }}</textarea>
        @error('material_certifications')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_last_audit_date" class="block text-sm font-medium text-gray-700 mb-1">Last Audit Date</label>
        <input id="f_last_audit_date" type="date" name="last_audit_date" value="{{ old('last_audit_date', optional($supplier->last_audit_date ?? null)->format('Y-m-d')) }}"
               class="{{ $base }} {{ $errors->has('last_audit_date') ? $err : $ok }}">
        @error('last_audit_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="f_status" name="status" class="{{ $base }} {{ $errors->has('status') ? $err : $ok }}">
            @foreach (['Active', 'Inactive', 'Draft'] as $s)
                <option value="{{ $s }}" @selected(old('status', $supplier->status ?? 'Active') === $s)>{{ $s }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
