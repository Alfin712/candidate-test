@props(['user' => null])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" required value="{{ old('name', $user->name ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" required value="{{ old('email', $user->email ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password @if (!$user) <span class="text-red-500">*</span> @endif</label>
        <input type="password" name="password" @if (!$user) required @endif class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        @if ($user)<p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>@endif
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select name="role" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            @foreach (\App\Models\User::roles() as $r)
                <option value="{{ $r }}" @selected(old('role', $user->role ?? 'viewer') === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
    </div>
</div>
