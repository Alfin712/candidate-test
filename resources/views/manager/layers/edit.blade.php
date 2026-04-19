@extends('layouts.manager')
@section('title', 'Edit Layer')

@section('content')
@include('manager.partials.breadcrumb', ['items' => [
    ['label' => 'Inventory', 'url' => route('dashboard')],
    ['label' => $layup->name, 'url' => route('layups.show', $layup)],
    ['label' => 'Edit Layer L'.$layer->layer_order],
]])
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 max-w-3xl">
    <h1 class="text-xl font-semibold text-gray-900 mb-1">Edit Layer</h1>
    <p class="text-sm text-gray-500 mb-4">Layup: <span class="font-medium text-gray-700">{{ $layup->name }}</span></p>
    <form method="POST" action="{{ route('layers.update', $layer) }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf @method('PUT')
        @include('manager.layers._form', ['layer' => $layer])
        <div class="mt-6 flex gap-2">
            <button type="submit" :disabled="submitting"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed min-h-[40px]">
                <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="submitting ? 'Saving...' : 'Save Changes'">Save Changes</span>
            </button>
            <a href="{{ route('layups.show', $layup) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 min-h-[40px] inline-flex items-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
