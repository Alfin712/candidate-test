@extends('layouts.manager')
@section('title', 'Layups')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Layup Specifications</h1>
        <p class="text-sm text-gray-500 mt-1">Browse every layup across the network.</p>
    </div>
    <a href="{{ route('layups.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Layup
    </a>
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search layups..."
                   class="flex-1 min-w-[220px] border border-gray-300 rounded-md text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            <select name="supplier_id" class="border border-gray-300 rounded-md text-sm px-3 py-2">
                <option value="">All Suppliers</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
            <select name="status" class="border border-gray-300 rounded-md text-sm px-3 py-2">
                <option value="">All Status</option>
                @foreach (['Active', 'Draft', 'Archived'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Spec Code</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Supplier</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Ply / Layers</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Grade</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Status</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-500 uppercase text-xs tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($layups as $layup)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-600">{{ $layup->specification_code ?? '—' }}</td>
                        <td class="px-5 py-3"><a href="{{ route('layups.show', $layup) }}" class="font-medium text-gray-900 hover:text-emerald-700">{{ $layup->name }}</a></td>
                        <td class="px-5 py-3 text-gray-600">
                            <a href="{{ route('suppliers.show', $layup->supplier_id) }}" class="hover:text-emerald-700">{{ $layup->supplier?->name }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->ply_count ?? '—' }} / {{ $layup->layers_count }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->grade ?? '—' }}</td>
                        <td class="px-5 py-3">@include('manager.partials.status-badge', ['status' => $layup->status])</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('layups.show', $layup) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">View</a>
                            <a href="{{ route('layups.edit', $layup) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">Edit</a>
                            <form method="POST" action="{{ route('layups.destroy', $layup) }}" class="inline" onsubmit="return confirm('Delete layup?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:text-red-800 px-2 py-1">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No layups found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div class="text-xs text-gray-500">
            Showing {{ $layups->firstItem() ?? 0 }} to {{ $layups->lastItem() ?? 0 }} of {{ $layups->total() }} results
        </div>
        <div>{{ $layups->links() }}</div>
    </div>
</div>
@endsection
