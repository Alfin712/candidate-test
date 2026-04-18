@extends('layouts.manager')
@section('title', $layup->name)

@php
    $totalThickness = $layup->layers->sum(fn ($l) => (float) $l->thickness);
    $colors = ['bg-amber-200', 'bg-emerald-200', 'bg-sky-200', 'bg-rose-200', 'bg-indigo-200', 'bg-lime-200'];
@endphp

@section('content')
<div class="mb-4 text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Home</a> &rsaquo;
    <a href="{{ route('suppliers.index') }}" class="hover:text-emerald-700">Suppliers</a> &rsaquo;
    <a href="{{ route('suppliers.show', $layup->supplier_id) }}" class="hover:text-emerald-700">{{ $layup->supplier?->name }}</a> &rsaquo;
    <a href="{{ route('layups.index') }}" class="hover:text-emerald-700">Layups</a> &rsaquo;
    {{ $layup->specification_code ?? $layup->name }}
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-semibold text-gray-900">{{ $layup->specification_code ?? $layup->name }}</h1>
                @include('manager.partials.status-badge', ['status' => $layup->status])
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $layup->description ?? 'No description provided.' }}</p>
        </div>
        @if (auth()->user()->canManage())
        <div class="flex gap-2">
            <a href="{{ route('layups.edit', $layup) }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('layups.destroy', $layup) }}" onsubmit="return confirm('Delete layup?')">
                @csrf @method('DELETE')
                <button class="px-3 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50">Delete</button>
            </form>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase">Supplier</div>
            <div class="mt-1 text-sm text-gray-900">{{ $layup->supplier?->name ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase">Grade</div>
            <div class="mt-1 text-sm text-gray-900">{{ $layup->grade ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase">Total Thickness</div>
            <div class="mt-1 text-sm text-gray-900">{{ number_format($totalThickness, 2) }} mm</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase">Total Layers</div>
            <div class="mt-1 text-sm text-gray-900">{{ $layup->layers->count() }} / Ply: {{ $layup->ply_count ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Layer Composition</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Thickness (mm)</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Width (mm)</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Angle (°)</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($layup->layers as $i => $layer)
                        <tr>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-sm {{ $colors[$i % count($colors)] }}"></span>
                                    L{{ $layer->layer_order }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $layer->thickness }}</td>
                            <td class="px-4 py-2">{{ $layer->width }}</td>
                            <td class="px-4 py-2">{{ $layer->angle }}°</td>
                            <td class="px-4 py-2">{{ $layer->grade ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm">No layers defined.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Structure Visualizer</h2>
        </div>
        <div class="p-4">
            @if ($layup->layers->isNotEmpty())
                <div class="text-xs text-gray-400 text-center mb-2">TOP</div>
                <div class="space-y-1">
                    @foreach ($layup->layers as $i => $layer)
                        @php
                            $height = max(20, min(60, (float) $layer->thickness * 1.5));
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 w-6">L{{ $layer->layer_order }}</span>
                            <div class="flex-1 {{ $colors[$i % count($colors)] }} rounded flex items-center justify-between px-3 text-xs text-gray-700"
                                 style="height: {{ $height }}px">
                                <span>{{ $layer->thickness }}mm</span>
                                <span>{{ $layer->angle }}°</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-xs text-gray-400 text-center mt-2">BOTTOM</div>
            @else
                <div class="text-center text-sm text-gray-500 py-8">No layers to visualize.</div>
            @endif
        </div>
    </div>
</div>

@if ($layup->description)
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-900">
        <div class="font-medium mb-1">Engineering Note</div>
        {{ $layup->description }}
    </div>
@endif
@endsection
