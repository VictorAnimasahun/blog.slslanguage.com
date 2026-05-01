@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Published Posts</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['published_posts'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Draft Posts</p>
        <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $stats['draft_posts'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Pending Comments</p>
        <p class="text-3xl font-bold text-red-500 mt-1">{{ $stats['pending_comments'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Total Posts</p>
        <p class="text-3xl font-bold text-gray-700 mt-1">{{ $stats['total_posts'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Total Comments</p>
        <p class="text-3xl font-bold text-gray-700 mt-1">{{ $stats['total_comments'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">Categories</p>
        <p class="text-3xl font-bold text-gray-700 mt-1">{{ $stats['total_categories'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Posts -->
    <div class="bg-white rounded-lg shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-700">Recent Posts</h2>
            <a href="{{ route('admin.posts.create') }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                + New Post
            </a>
        </div>
        <ul class="divide-y">
            @forelse($recentPosts as $post)
            <li class="px-5 py-3 flex items-center justify-between text-sm">
                <div class="min-w-0">
                    <p class="font-medium text-gray-800 truncate">{{ $post->title }}</p>
                    <p class="text-gray-400 text-xs">{{ $post->author->display_name ?? $post->author->first_name ?? 'Unknown' }} &middot; {{ $post->created_at->format('M j, Y') }}</p>
                </div>
                <span class="ml-3 shrink-0 px-2 py-0.5 rounded text-xs font-medium
                    {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $post->status }}
                </span>
            </li>
            @empty
            <li class="px-5 py-4 text-sm text-gray-400">No posts yet.</li>
            @endforelse
        </ul>
        <div class="px-5 py-3 border-t">
            <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:underline">View all posts →</a>
        </div>
    </div>

    <!-- Pending Comments -->
    <div class="bg-white rounded-lg shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-700">Pending Comments</h2>
            <a href="{{ route('admin.comments.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
        <ul class="divide-y">
            @forelse($pendingComments as $comment)
            <li class="px-5 py-3 text-sm">
                <p class="font-medium text-gray-700">{{ $comment->guest_name ?? ($comment->user->display_name ?? 'User') }}</p>
                <p class="text-gray-500 text-xs mb-1">on <span class="italic">{{ $comment->post->title ?? '—' }}</span></p>
                <p class="text-gray-600 line-clamp-2">{{ $comment->content }}</p>
                <div class="flex gap-3 mt-2">
                    <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                        @csrf @method('PATCH')
                        <button class="text-green-600 hover:underline text-xs">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.comments.spam', $comment) }}">
                        @csrf @method('PATCH')
                        <button class="text-yellow-600 hover:underline text-xs">Spam</button>
                    </form>
                    <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </div>
            </li>
            @empty
            <li class="px-5 py-4 text-sm text-gray-400">No pending comments.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
