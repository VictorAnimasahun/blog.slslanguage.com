@extends('layouts.app')

@section('content')
<div class="grid grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="col-span-2 bg-white p-8 rounded-lg shadow-sm">
        <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>
        
        <p class="text-gray-600 text-sm mb-8">
            Posted on {{ $post->published_at->format('F j, Y') }} by 
            <span class="text-pink-600 font-semibold">{{ $post->author->display_name }}</span>
            in <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600 hover:underline">{{ $post->category->name }}</a>
        </p>

        <div class="prose prose-lg max-w-none mb-8">
            {!! $post->content !!}
        </div>

        <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline font-semibold">
            ← Back to Blog
        </a>
    </div>

    <!-- Sidebar -->
    <div class="col-span-1">
        <!-- Post Info -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <h3 class="section-title font-bold text-lg">Post Info</h3>
            <p class="text-gray-700 mb-3">
                <strong>Author:</strong> {{ $post->author->display_name }}
            </p>
            <p class="text-gray-700 mb-3">
                <strong>Category:</strong> 
                <a href="{{ route('blog.category', $post->category->slug) }}" class="text-blue-600">
                    {{ $post->category->name }}
                </a>
            </p>
            <p class="text-gray-700">
                <strong>Published:</strong> {{ $post->published_at->format('F j, Y') }}
            </p>
        </div>

        @if($relatedPosts->count())
            <!-- Related Posts -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="section-title font-bold text-lg">Related Posts</h3>
                <ul class="space-y-3">
                    @foreach($relatedPosts as $related)
                        <li>
                            <a href="{{ route('blog.show', $related->slug) }}" class="text-blue-600 hover:underline font-semibold">
                                {{ $related->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection