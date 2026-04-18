@extends('layouts.manager')
@section('title', 'New Supplier')

@section('content')
<div class="mb-4 text-sm text-gray-500">
    <a href="{{ route('suppliers.index') }}" class="hover:text-emerald-700">Suppliers</a> &rsaquo; New
</div>
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 max-w-3xl">
    <h1 class="text-xl font-semibold text-gray-900 mb-4">Create Supplier</h1>
    <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        @include('manager.suppliers._form')
        <div class="mt-6 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">Create Supplier</button>
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
