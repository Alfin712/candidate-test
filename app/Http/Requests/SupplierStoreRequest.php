<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;

class SupplierStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Supplier::class) ?? true;
    }

    public function rules(): array
    {
        return [
            'code'                    => ['nullable', 'string', 'max:50', 'unique:suppliers,code'],
            'name'                    => ['required', 'string', 'max:255', 'unique:suppliers,name'],
            'primary_contact'         => ['nullable', 'string', 'max:255'],
            'location'                => ['nullable', 'string', 'max:255'],
            'material_certifications' => ['nullable', 'string'],
            'last_audit_date'         => ['nullable', 'date'],
            'status'                  => ['nullable', 'string', 'max:100'],
        ];
    }
}

