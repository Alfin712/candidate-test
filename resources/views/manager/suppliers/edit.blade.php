@extends('layouts.manager')
@section('title', 'Edit Supplier')

@section('content')
@include('manager.partials.breadcrumb', ['items' => [
    ['label' => 'Suppliers', 'url' => route('suppliers.index')],
    ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
    ['label' => 'Edit'],
]])
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 max-w-3xl">
    <h1 class="text-xl font-semibold text-gray-900 mb-4">Edit Supplier</h1>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf @method('PUT')
        @include('manager.suppliers._form', ['supplier' => $supplier])
        <div class="mt-6 flex gap-2">
            <button type="submit" :disabled="submitting"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed min-h-[40px]">
                <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="submitting ? 'Saving...' : 'Save Changes'">Save Changes</span>
            </button>
            <a href="{{ route('suppliers.show', $supplier) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 min-h-[40px] inline-flex items-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
