@extends('admin.layout')

@section('title', 'Posts')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Posts</h1>
    <a href="{{ route('admin.posts.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
        + New Post
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs tracking-wide">
            <tr>
                <th class="text-left px-5 py-3">Title</th>
                <th class="text-left px-5 py-3">Category</th>
                <th class="text-left px-5 py-3">Status</th>
                <th class="text-left px-5 py-3">Date</th>
                <th class="text-left px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($posts as $post)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800">{{ $post->title }}</p>
                    <p class="text-gray-400 text-xs">{{ $post->author->display_name ?? $post->author->first_name ?? '—' }}</p>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $post->category->name ?? '—' }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $post->status }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-400">{{ $post->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3">
                    <div class="flex gap-3">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:underline">Edit</a>
                        @if($post->status === 'published')
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-gray-400 hover:underline">View</a>
                        @endif
                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                              onsubmit="return confirm('Delete this post?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-gray-400">No posts yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($posts->hasPages())
    <div class="px-5 py-4 border-t">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
