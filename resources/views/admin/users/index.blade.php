@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Users</h1>
    <a href="{{ route('admin.users.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
        + New User
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs tracking-wide">
            <tr>
                <th class="text-left px-5 py-3">Name</th>
                <th class="text-left px-5 py-3">Email</th>
                <th class="text-left px-5 py-3">Role</th>
                <th class="text-left px-5 py-3">Status</th>
                <th class="text-left px-5 py-3">Posts</th>
                <th class="text-left px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 {{ $user->id === auth()->id() ? 'bg-blue-50' : '' }}">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800">{{ $user->display_name }}</p>
                    <p class="text-gray-400 text-xs">{{ $user->first_name }} {{ $user->last_name }}</p>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                           ($user->role === 'editor' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $user->status }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $user->posts_count }}</td>
                <td class="px-5 py-3">
                    <div class="flex gap-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ $user->display_name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Delete</button>
                            </form>
                        @else
                            <span class="text-gray-300 text-xs italic">you</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-400">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
