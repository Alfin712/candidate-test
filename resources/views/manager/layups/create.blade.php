@extends('layouts.manager')
@section('title', 'New Layup')

@section('content')
<div class="mb-4 text-sm text-gray-500">
    <a href="{{ route('layups.index') }}" class="hover:text-emerald-700">Layups</a> &rsaquo; New
</div>
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 max-w-3xl">
    <h1 class="text-xl font-semibold text-gray-900 mb-4">Create Layup</h1>
    <form method="POST" action="{{ route('layups.store') }}">
        @csrf
        @include('manager.layups._form', ['suppliers' => $suppliers, 'selectedSupplierId' => $selectedSupplierId])
        <div class="mt-6 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">Create Layup</button>
            <a href="{{ route('layups.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
