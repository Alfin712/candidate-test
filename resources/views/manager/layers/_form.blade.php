@props(['layer' => null])
@php
    $base = 'w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500';
    $err = 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500';
    $ok = 'border-gray-300';
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="f_layer_order" class="block text-sm font-medium text-gray-700 mb-1">Layer Order <span class="text-red-500">*</span></label>
        <input id="f_layer_order" type="number" name="layer_order" min="1" step="1" required
               value="{{ old('layer_order', $layer->layer_order ?? '') }}"
               aria-invalid="{{ $errors->has('layer_order') ? 'true' : 'false' }}"
               aria-describedby="h_layer_order"
               class="{{ $base }} {{ $errors->has('layer_order') ? $err : $ok }}">
        <p id="h_layer_order" class="mt-1 text-xs text-gray-500">Position in the stack (1 = top). Must be unique within this layup.</p>
        @error('layer_order')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_thickness" class="block text-sm font-medium text-gray-700 mb-1">Thickness <span class="text-red-500">*</span></label>
        <div class="relative">
            <input id="f_thickness" type="number" name="thickness" min="0.1" step="0.01" required
                   value="{{ old('thickness', $layer->thickness ?? '') }}"
                   aria-invalid="{{ $errors->has('thickness') ? 'true' : 'false' }}"
                   aria-describedby="h_thickness"
                   class="{{ $base }} pr-10 {{ $errors->has('thickness') ? $err : $ok }}">
            <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400 pointer-events-none">mm</span>
        </div>
        <p id="h_thickness" class="mt-1 text-xs text-gray-500">Minimum 0.1 mm.</p>
        @error('thickness')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_width" class="block text-sm font-medium text-gray-700 mb-1">Width <span class="text-red-500">*</span></label>
        <div class="relative">
            <input id="f_width" type="number" name="width" min="0.1" step="0.01" required
                   value="{{ old('width', $layer->width ?? '') }}"
                   aria-invalid="{{ $errors->has('width') ? 'true' : 'false' }}"
                   aria-describedby="h_width"
                   class="{{ $base }} pr-10 {{ $errors->has('width') ? $err : $ok }}">
            <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400 pointer-events-none">mm</span>
        </div>
        <p id="h_width" class="mt-1 text-xs text-gray-500">Minimum 0.1 mm.</p>
        @error('width')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_angle" class="block text-sm font-medium text-gray-700 mb-1">Angle</label>
        <div class="relative">
            <input id="f_angle" type="number" name="angle" min="0" max="180" step="1"
                   value="{{ old('angle', $layer->angle ?? '') }}"
                   aria-invalid="{{ $errors->has('angle') ? 'true' : 'false' }}"
                   aria-describedby="h_angle"
                   class="{{ $base }} pr-10 {{ $errors->has('angle') ? $err : $ok }}">
            <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400 pointer-events-none">deg</span>
        </div>
        <p id="h_angle" class="mt-1 text-xs text-gray-500">Fiber orientation, 0–180 degrees. Leave blank if unspecified.</p>
        @error('angle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="f_grade" class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
        <input id="f_grade" type="text" name="grade" maxlength="50"
               value="{{ old('grade', $layer->grade ?? '') }}"
               aria-invalid="{{ $errors->has('grade') ? 'true' : 'false' }}"
               class="{{ $base }} {{ $errors->has('grade') ? $err : $ok }}">
        @error('grade')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
