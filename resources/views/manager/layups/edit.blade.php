@extends('layouts.manager')
@section('title', 'Edit Layup')

@section('content')
<div class="mb-4 text-sm text-gray-500">
    <a href="{{ route('layups.index') }}" class="hover:text-emerald-700">Layups</a> &rsaquo;
    <a href="{{ route('layups.show', $layup) }}" class="hover:text-emerald-700">{{ $layup->name }}</a> &rsaquo; Edit
</div>
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 max-w-3xl">
    <h1 class="text-xl font-semibold text-gray-900 mb-4">Edit Layup</h1>
    <form method="POST" action="{{ route('layups.update', $layup) }}">
        @csrf @method('PUT')
        @include('manager.layups._form', ['layup' => $layup, 'suppliers' => $suppliers])
        <div class="mt-6 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">Save Changes</button>
            <a href="{{ route('layups.show', $layup) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
