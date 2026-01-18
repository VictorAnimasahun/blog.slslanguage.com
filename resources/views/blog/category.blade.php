@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-2">{{ $category->name }}</h1>
    
    @if($category->description)
        <p class="text-gray-600 mb-8">{{ $category->description }}</p>
    @endif

    @forelse($posts as $post)
        <article class="mb-8 pb-8 border-b">
            <h2 class="text-2xl font-bold mb-2">
                <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 hover:underline">
                    {{ $post->title }}
                </a>
            </h2>
            
            <div class="text-gray-600 text-sm mb-4">
                By <strong>{{ $post->author->display_name }}</strong> 
                · {{ $post->published_at->format('F j, Y') }}
            </div>

            <p class="text-gray-700 mb-4">{{ $post->excerpt ?? Str::limit($post->content, 150) }}</p>

            <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 font-semibold hover:underline">
                Read More →
            </a>
        </article>
    @empty
        <p class="text-gray-600">No posts in this category.</p>
    @endforelse

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $posts->links() }}
    </div>

    <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline">
        ← Back to Blog
    </a>
</div>
@endsection