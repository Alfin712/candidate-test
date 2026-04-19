@extends('layouts.manager')
@section('title', 'Users')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">User Management</h1>
        <p class="text-sm text-gray-500 mt-1">Manage users and roles. Admins only.</p>
    </div>
    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add User
    </a>
</div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search users..."
                   class="flex-1 min-w-[220px] border border-gray-300 rounded-md text-sm px-3 py-2">
            <select name="role" class="border border-gray-300 rounded-md text-sm px-3 py-2">
                <option value="">All Roles</option>
                @foreach (\App\Models\User::roles() as $r)
                    <option value="{{ $r }}" @selected($role === $r)>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">Filter</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold flex items-center justify-center">{{ $u->initials() }}</div>
                                <div class="font-medium text-gray-900">{{ $u->name }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-3">
                            @php $roleClass = [
                                'admin'    => 'bg-rose-100 text-rose-700',
                                'staff'    => 'bg-emerald-100 text-emerald-700',
                                'engineer' => 'bg-indigo-100 text-indigo-700',
                                'viewer'   => 'bg-gray-100 text-gray-700',
                            ][$u->role] ?? 'bg-gray-100 text-gray-700'; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $roleClass }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $u->created_at?->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('users.edit', $u) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">Edit</a>
                            @if ($u->id !== auth()->id())
                                @include('manager.partials.delete-confirm', [
                                    'action' => route('users.destroy', $u),
                                    'title' => 'Delete user',
                                    'message' => 'Delete user "'.$u->name.'"? They will lose access immediately.',
                                    'triggerClass' => 'text-xs text-red-600 hover:text-red-800 px-2 py-1',
                                ])
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div class="text-xs text-gray-500">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
        </div>
        <div>{{ $users->links() }}</div>
    </div>
</div>
@endsection
