@extends('layouts.manager')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Overview of your CLT supplier network and layup specifications.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Supplier
        </a>
        <a href="{{ route('layups.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Layup
        </a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Suppliers</div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalSuppliers }}</div>
        <div class="mt-1 text-xs text-gray-500">
            <span class="text-emerald-600 font-medium">{{ $activeSuppliers }} active</span>
            &middot; {{ $inactiveSuppliers }} inactive
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Layups</div>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </div>
        </div>
        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalLayups }}</div>
        <div class="mt-1 text-xs text-gray-500">Specifications across all suppliers</div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Layers</div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalLayers }}</div>
        <div class="mt-1 text-xs text-gray-500">Individual ply entries</div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Quick Actions</div>
        <div class="space-y-2">
            <a href="{{ route('suppliers.create') }}" class="block text-sm text-emerald-700 hover:underline">+ New Supplier</a>
            <a href="{{ route('layups.create') }}" class="block text-sm text-emerald-700 hover:underline">+ New Layup</a>
            <a href="{{ route('suppliers.index') }}" class="block text-sm text-emerald-700 hover:underline">Import Data</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Recent Layups</h2>
            <a href="{{ route('layups.index') }}" class="text-xs text-emerald-700 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($recentLayups as $layup)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <a href="{{ route('layups.show', $layup) }}" class="font-medium text-gray-900 hover:text-emerald-700">{{ $layup->name }}</a>
                        <div class="text-xs text-gray-500">
                            {{ $layup->supplier?->name ?? '—' }}
                            @if ($layup->specification_code) &middot; {{ $layup->specification_code }} @endif
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">{{ $layup->updated_at?->diffForHumans() }}</div>
                </div>
            @empty
                <div class="px-5 py-6 text-sm text-gray-500 text-center">No layups yet.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Recent Suppliers</h2>
            <a href="{{ route('suppliers.index') }}" class="text-xs text-emerald-700 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($recentSuppliers as $supplier)
                <a href="{{ route('suppliers.show', $supplier) }}" class="block px-5 py-3 hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold flex items-center justify-center">
                            {{ mb_strtoupper(mb_substr($supplier->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $supplier->name }}</div>
                            <div class="text-xs text-gray-500">{{ $supplier->location ?? '—' }}</div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-5 py-6 text-sm text-gray-500 text-center">No suppliers yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
