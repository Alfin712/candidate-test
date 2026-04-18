@extends('layouts.manager')
@section('title', 'Suppliers')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Suppliers</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your CLT supplier network.</p>
    </div>
    <div class="flex gap-2">
        @if (auth()->user()->canManage())
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Supplier
        </a>
        @endif
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-3 flex-wrap">
        <form method="GET" class="flex-1 flex gap-2 min-w-[260px]">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search suppliers..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <select name="status" class="border border-gray-300 rounded-md text-sm pl-3 pr-8 py-2 bg-white focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">All Status</option>
                <option value="Active" @selected($status === 'Active')>Active</option>
                <option value="Inactive" @selected($status === 'Inactive')>Inactive</option>
                <option value="Draft" @selected($status === 'Draft')>Draft</option>
            </select>
            <button class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Supplier</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Code</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Location</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Layups</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Status</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Created</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-500 uppercase text-xs tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($suppliers as $supplier)
                    @php
                        $palette = ['bg-emerald-100 text-emerald-700', 'bg-indigo-100 text-indigo-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700', 'bg-sky-100 text-sky-700'];
                        $color = $palette[$supplier->id % count($palette)];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $color }} text-xs font-semibold flex items-center justify-center">
                                    {{ mb_strtoupper(mb_substr($supplier->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="font-medium text-gray-900 hover:text-emerald-700">{{ $supplier->name }}</a>
                                    <div class="text-xs text-gray-500">ID #{{ $supplier->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $supplier->code ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $supplier->location ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $supplier->layups_count }}</span></td>
                        <td class="px-5 py-3">@include('manager.partials.status-badge', ['status' => $supplier->status])</td>
                        <td class="px-5 py-3 text-gray-600">{{ $supplier->created_at?->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex gap-1" x-data="{ open: false }">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1 rounded hover:bg-gray-100">Detail</a>
                                @if (auth()->user()->canManage())
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1 rounded hover:bg-gray-100">Edit</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete supplier {{ $supplier->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div class="text-xs text-gray-500">
            Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} results
        </div>
        <div>{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
