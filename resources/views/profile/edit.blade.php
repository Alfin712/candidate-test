@extends('layouts.manager')
@section('title', 'Profile')

@section('content')
@include('manager.partials.breadcrumb', ['items' => [
    ['label' => 'Profile'],
]])

<div class="space-y-6 max-w-3xl">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <h1 class="text-xl font-semibold text-gray-900 mb-1">Profile Information</h1>
        <p class="text-sm text-gray-500 mb-6">Update your account's profile information and email address.</p>
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-1">Update Password</h2>
        <p class="text-sm text-gray-500 mb-6">Ensure your account is using a long, random password to stay secure.</p>
        @include('profile.partials.update-password-form')
    </div>

    <div class="bg-white rounded-lg border border-rose-200 shadow-sm p-6">
        <h2 class="text-xl font-semibold text-rose-700 mb-1">Delete Account</h2>
        <p class="text-sm text-gray-500 mb-6">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
