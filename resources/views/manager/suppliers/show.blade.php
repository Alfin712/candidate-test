@extends('layouts.manager')
@section('title', $supplier->name)

@section('content')
<div class="mb-4 text-sm text-gray-500">
    <a href="{{ route('suppliers.index') }}" class="hover:text-emerald-700">Suppliers</a> &rsaquo; {{ $supplier->name }}
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-700 text-lg font-semibold flex items-center justify-center">
                {{ mb_strtoupper(mb_substr($supplier->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $supplier->name }}</h1>
                    @include('manager.partials.status-badge', ['status' => $supplier->status])
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    @if ($supplier->code) Code: {{ $supplier->code }} &middot; @endif
                    ID #{{ $supplier->id }}
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier?')">
                @csrf @method('DELETE')
                <button class="px-3 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Primary Contact</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->primary_contact ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->location ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Material Certifications</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->material_certifications ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Audit Date</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->last_audit_date?->format('M d, Y') ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div>
            <h2 class="font-semibold text-gray-900">Associated Layups</h2>
            <p class="text-xs text-gray-500">{{ $supplier->layups_count }} total</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="alert('Import UI stub — use API /api/suppliers/{{ $supplier->id }}/import')" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Import</button>
            <a href="{{ url('/api/suppliers/'.$supplier->id.'/export') }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Export</a>
            <a href="{{ route('layups.create', ['supplier_id' => $supplier->id]) }}" class="px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">Add Layup</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Spec Code</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Ply Count</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Layers</th>
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
                        <td class="px-5 py-3 text-gray-600">{{ $layup->ply_count ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->layers_count }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->grade ?? '—' }}</td>
                        <td class="px-5 py-3">@include('manager.partials.status-badge', ['status' => $layup->status])</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('layups.show', $layup) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">View</a>
                            <a href="{{ route('layups.edit', $layup) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No layups yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
