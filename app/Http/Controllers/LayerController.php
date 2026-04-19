<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayerController extends Controller
{
    public function create(Layup $layup)
    {
        $layup->load('supplier');
        $layer = new Layer();
        return view('manager.layers.create', compact('layup', 'layer'));
    }

    public function store(Request $request, Layup $layup)
    {
        $data = $this->validateData($request, $layup);
        $data['layup_id'] = $layup->id;
        Layer::create($data);
        return redirect()->route('layups.show', $layup)->with('success', 'Layer created.');
    }

    public function edit(Layer $layer)
    {
        $layup = $layer->layup()->with('supplier')->firstOrFail();
        abort_unless($layer->layup_id === $layup->id, 404);
        return view('manager.layers.edit', compact('layup', 'layer'));
    }

    public function update(Request $request, Layer $layer)
    {
        $layup = $layer->layup()->firstOrFail();
        abort_unless($layer->layup_id === $layup->id, 404);
        $data = $this->validateData($request, $layup, $layer);
        $layer->update($data);
        return redirect()->route('layups.show', $layup)->with('success', 'Layer updated.');
    }

    public function destroy(Layer $layer)
    {
        $layup = $layer->layup()->firstOrFail();
        abort_unless($layer->layup_id === $layup->id, 404);
        $layer->delete();
        return redirect()->route('layups.show', $layup)->with('success', 'Layer deleted.');
    }

    protected function validateData(Request $request, Layup $layup, ?Layer $layer = null): array
    {
        $uniqueRule = Rule::unique('clt_layers', 'layer_order')
            ->where(fn ($q) => $q->where('layup_id', $layup->id));
        if ($layer) {
            $uniqueRule = $uniqueRule->ignore($layer->id);
        }

        return $request->validate([
            'layer_order' => ['required', 'integer', 'min:1', $uniqueRule],
            'thickness' => ['required', 'numeric', 'min:0.1'],
            'width' => ['required', 'numeric', 'min:0.1'],
            'angle' => ['nullable', 'integer', 'between:0,180'],
            'grade' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
