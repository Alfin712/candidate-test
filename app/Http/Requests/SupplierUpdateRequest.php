<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return $this->user()?->can('update', $supplier) ?? true;
    }

    public function rules(): array
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return [
            'code'                    => ['nullable', 'string', 'max:50', Rule::unique('suppliers', 'code')->ignore($supplier->id)],
            'name'                    => ['sometimes', 'required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplier->id)],
            'primary_contact'         => ['nullable', 'string', 'max:255'],
            'location'                => ['nullable', 'string', 'max:255'],
            'material_certifications' => ['nullable', 'string'],
            'last_audit_date'         => ['nullable', 'date'],
            'status'                  => ['nullable', 'string', 'max:100'],
        ];
    }
}
