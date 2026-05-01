@extends('admin.layout')

@section('title', 'Comments')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-4">Comments</h1>

<!-- Filter tabs -->
<div class="flex gap-1 mb-6 bg-white rounded-lg shadow p-1 w-fit">
    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'spam' => 'Spam', 'all' => 'All'] as $key => $label)
        <a href="{{ route('admin.comments.index', ['status' => $key]) }}"
           class="px-4 py-1.5 rounded text-sm font-medium transition
               {{ $status === $key ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            {{ $label }}
            @if(isset($counts[$key]) && $counts[$key] > 0)
                <span class="ml-1 text-xs {{ $status === $key ? 'opacity-75' : 'text-gray-400' }}">({{ $counts[$key] }})</span>
            @endif
        </a>
    @endforeach
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs tracking-wide">
            <tr>
                <th class="text-left px-5 py-3">Author</th>
                <th class="text-left px-5 py-3">Comment</th>
                <th class="text-left px-5 py-3">Post</th>
                <th class="text-left px-5 py-3">Status</th>
                <th class="text-left px-5 py-3">Date</th>
                <th class="text-left px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($comments as $comment)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800">
                        {{ $comment->guest_name ?? ($comment->user->display_name ?? $comment->user->first_name ?? 'User') }}
                    </p>
                    @if($comment->guest_email)
                        <p class="text-gray-400 text-xs">{{ $comment->guest_email }}</p>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-600 max-w-xs">
                    <p class="line-clamp-2">{{ $comment->content }}</p>
                </td>
                <td class="px-5 py-3 text-gray-500">
                    @if($comment->post)
                        <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank"
                           class="hover:underline text-blue-600 line-clamp-1">
                            {{ $comment->post->title }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700' :
                           ($comment->status === 'spam' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $comment->status }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-400 whitespace-nowrap">{{ $comment->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3">
                    <div class="flex gap-3 flex-wrap">
                        @if($comment->status !== 'approved')
                            <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                                @csrf @method('PATCH')
                                <button class="text-green-600 hover:underline text-xs">Approve</button>
                            </form>
                        @endif
                        @if($comment->status !== 'spam')
                            <form method="POST" action="{{ route('admin.comments.spam', $comment) }}">
                                @csrf @method('PATCH')
                                <button class="text-yellow-600 hover:underline text-xs">Spam</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}"
                              onsubmit="return confirm('Delete this comment?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-400">No comments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($comments->hasPages())
    <div class="px-5 py-4 border-t">
        {{ $comments->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
