<!-- Comments Section -->
<div class="mt-12 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold mb-6 text-gray-800">
        Comments ({{ $post->approvedComments->count() }})
    </h3>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Comment Form -->
    <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <h4 class="text-lg font-semibold mb-4">Leave a Comment</h4>
        
        <form action="{{ route('blog.comment.store', $post->slug) }}" method="POST">
            @csrf
            
            @guest
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" 
                               name="guest_name" 
                               value="{{ old('guest_name') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_name') border-red-500 @enderror"
                               required>
                        @error('guest_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" 
                               name="guest_email" 
                               value="{{ old('guest_email') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_email') border-red-500 @enderror"
                               required>
                        @error('guest_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endguest
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Comment *</label>
                <textarea name="content" 
                          rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror"
                          placeholder="Share your thoughts..."
                          required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" 
                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition-colors">
                Post Comment
            </button>
        </form>
    </div>

    <!-- Display Comments -->
    <div class="space-y-6">
        @forelse($post->approvedComments as $comment)
            <div class="border-l-4 border-blue-500 pl-4 py-2">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold">
                            {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $comment->author_name }}</p>
                            <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $comment->content }}</p>
            </div>
        @empty
            <p class="text-gray-500 italic">No comments yet. Be the first to comment!</p>
        @endforelse
    </div>
</div>