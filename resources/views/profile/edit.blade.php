@extends('layouts.app')

@section('title', 'My Account')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">My Account</h1>

    @if(session('status') === 'profile-updated')
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
            Profile saved.
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
            Password updated.
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Profile Information</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                        :value="old('first_name', $user->first_name)" required />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="last_name" value="Last Name" />
                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                        :value="old('last_name', $user->last_name)" required />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="display_name" value="Display Name" />
                <p class="text-xs text-gray-400 mb-1">Shown publicly on posts and comments.</p>
                <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full"
                    :value="old('display_name', $user->display_name)" required />
                <x-input-error :messages="$errors->get('display_name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                    :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Update Password</h2>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <x-input-label for="current_password" value="Current Password" />
                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full"
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="new_password" value="New Password" />
                <x-text-input id="new_password" name="password" type="password" class="mt-1 block w-full"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirm New Password" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
            </div>

            <div>
                <x-primary-button>Update Password</x-primary-button>
            </div>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-red-100">
        <h2 class="text-lg font-semibold text-gray-700 mb-1">Delete Account</h2>
        <p class="text-sm text-gray-500 mb-4">
            Permanently deletes your account and all associated data. This cannot be undone.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Are you absolutely sure? This will permanently delete your account.')">
            @csrf @method('DELETE')

            <div class="mb-4">
                <x-input-label for="delete_password" value="Enter your password to confirm" />
                <x-text-input id="delete_password" name="password" type="password" class="mt-1 block w-full sm:w-1/2"
                    placeholder="Your current password" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <button type="submit"
                    class="bg-red-600 text-white px-5 py-2 rounded text-sm font-medium hover:bg-red-700">
                Delete My Account
            </button>
        </form>
    </div>
</div>
@endsection
