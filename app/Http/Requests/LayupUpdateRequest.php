<?php

namespace App\Http\Requests;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LayupUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Layup $layup */
        $layup = $this->route('layup');

        return $this->user()?->can('update', $layup) ?? true;
    }

    public function rules(): array
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');
        /** @var Layup $layup */
        $layup = $this->route('layup');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('clt_layups', 'name')
                    ->where(fn ($query) => $query->where('supplier_id', $supplier->id))
                    ->ignore($layup->id),
            ],
            'description'        => ['nullable', 'string'],
            'specification_code' => ['nullable', 'string', 'max:255'],
            'ply_count'          => ['nullable', 'integer', 'min:1'],
            'grade'              => ['nullable', 'string', 'max:255'],
            'status'             => ['nullable', 'string', 'max:100'],
        ];
    }
}
