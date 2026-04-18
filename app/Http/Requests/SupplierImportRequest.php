<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $supplier = $this->route('supplier');

        return $this->user()?->can('import', $supplier) ?? true;
    }

    public function rules(): array
    {
        return [
            'strategy' => ['nullable', Rule::in(['skip', 'overwrite', 'reject', 'duplicate', 'manual'])],
            'dry_run' => ['nullable', 'boolean'],
            'payload' => ['required', 'array'],
            'payload.name' => ['nullable', 'string', 'max:255'],
            'payload.primary_contact' => ['nullable', 'string', 'max:255'],
            'payload.location' => ['nullable', 'string', 'max:255'],
            'payload.material_certifications' => ['nullable', 'string'],
            'payload.last_audit_date' => ['nullable', 'date'],
            'payload.status' => ['nullable', 'string', 'max:100'],
            'payload.layups' => ['required', 'array'],
            'payload.layups.*.name' => ['required', 'string', 'max:255'],
            'payload.layups.*.description' => ['nullable', 'string'],
            'payload.layups.*.specification_code' => ['nullable', 'string', 'max:255'],
            'payload.layups.*.ply_count' => ['nullable', 'integer', 'min:1'],
            'payload.layups.*.grade' => ['nullable', 'string', 'max:255'],
            'payload.layups.*.status' => ['nullable', 'string', 'max:100'],
            'payload.layups.*.layers' => ['required', 'array'],
            'payload.layups.*.layers.*.layer_order' => ['required', 'integer', 'min:1'],
            'payload.layups.*.layers.*.thickness' => ['required', 'numeric', 'gt:0'],
            'payload.layups.*.layers.*.width' => ['required', 'numeric', 'gt:0'],
            'payload.layups.*.layers.*.angle' => ['required', 'numeric'],
            'payload.layups.*.layers.*.grade' => ['nullable', 'string', 'max:255'],
            'resolutions' => ['nullable', 'array'],
            'resolutions.*.layup_name' => ['required_with:resolutions', 'string', 'max:255'],
            'resolutions.*.layer_order' => ['required_with:resolutions', 'integer', 'min:1'],
            'resolutions.*.action' => ['required_with:resolutions', Rule::in(['keep_existing', 'accept_incoming'])],
        ];
    }
}

