<?php

namespace App\Http\Requests;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LayerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Layer $layer */
        $layer = $this->route('layer');

        return $this->user()?->can('update', $layer) ?? true;
    }

    public function rules(): array
    {
        /** @var Layup $layup */
        $layup = $this->route('layup');
        /** @var Layer $layer */
        $layer = $this->route('layer');

        return [
            'layer_order' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                Rule::unique('clt_layers', 'layer_order')
                    ->where(fn ($query) => $query->where('layup_id', $layup->id))
                    ->ignore($layer->id),
            ],
            'thickness' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'width'     => ['sometimes', 'required', 'numeric', 'gt:0'],
            'angle'     => ['sometimes', 'required', 'numeric'],
            'grade'     => ['nullable', 'string', 'max:255'],
        ];
    }
}
