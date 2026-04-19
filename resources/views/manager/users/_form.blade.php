@props(['user' => null])
@php
    $base = 'w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500';
    $err = 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500';
    $ok = 'border-gray-300';
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="f_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
        <input id="f_name" type="text" name="name" required value="{{ old('name', $user->name ?? '') }}" class="{{ $base }} {{ $errors->has('name') ? $err : $ok }}">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
        <input id="f_email" type="email" name="email" required value="{{ old('email', $user->email ?? '') }}" class="{{ $base }} {{ $errors->has('email') ? $err : $ok }}">
        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_password" class="block text-sm font-medium text-gray-700 mb-1">Password @if (!$user) <span class="text-red-500">*</span> @endif</label>
        <input id="f_password" type="password" name="password" autocomplete="new-password" @if (!$user) required @endif class="{{ $base }} {{ $errors->has('password') ? $err : $ok }}">
        @if ($user)<p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>@endif
        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input id="f_password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="{{ $base }} {{ $ok }}">
    </div>
    <div>
        <label for="f_role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select id="f_role" name="role" class="{{ $base }} {{ $errors->has('role') ? $err : $ok }}">
            @foreach (\App\Models\User::roles() as $r)
                <option value="{{ $r }}" @selected(old('role', $user->role ?? 'viewer') === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
