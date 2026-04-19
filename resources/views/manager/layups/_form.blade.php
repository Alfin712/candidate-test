@props(['layup' => null, 'suppliers', 'selectedSupplierId' => null])
@php
    $base = 'w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500';
    $err = 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500';
    $ok = 'border-gray-300';
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="f_supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
        <select id="f_supplier_id" name="supplier_id" required class="{{ $base }} {{ $errors->has('supplier_id') ? $err : $ok }}">
            <option value="">Select supplier...</option>
            @foreach ($suppliers as $s)
                <option value="{{ $s->id }}" @selected(old('supplier_id', $layup->supplier_id ?? $selectedSupplierId) == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
        @error('supplier_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input id="f_name" type="text" name="name" value="{{ old('name', $layup->name ?? '') }}" required
               class="{{ $base }} {{ $errors->has('name') ? $err : $ok }}">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_specification_code" class="block text-sm font-medium text-gray-700 mb-1">Specification Code</label>
        <input id="f_specification_code" type="text" name="specification_code" value="{{ old('specification_code', $layup->specification_code ?? '') }}"
               class="{{ $base }} {{ $errors->has('specification_code') ? $err : $ok }}">
        @error('specification_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_ply_count" class="block text-sm font-medium text-gray-700 mb-1">Ply Count</label>
        <input id="f_ply_count" type="number" name="ply_count" min="0" value="{{ old('ply_count', $layup->ply_count ?? '') }}"
               class="{{ $base }} {{ $errors->has('ply_count') ? $err : $ok }}">
        @error('ply_count')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_grade" class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
        <input id="f_grade" type="text" name="grade" value="{{ old('grade', $layup->grade ?? '') }}"
               class="{{ $base }} {{ $errors->has('grade') ? $err : $ok }}">
        @error('grade')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="f_status" name="status" class="{{ $base }} {{ $errors->has('status') ? $err : $ok }}">
            @foreach (['Active', 'Draft', 'Archived'] as $s)
                <option value="{{ $s }}" @selected(old('status', $layup->status ?? 'Active') === $s)>{{ $s }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="f_description" class="block text-sm font-medium text-gray-700 mb-1">Description / Engineering Note</label>
        <textarea id="f_description" name="description" rows="3" class="{{ $base }} {{ $errors->has('description') ? $err : $ok }}">{{ old('description', $layup->description ?? '') }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
