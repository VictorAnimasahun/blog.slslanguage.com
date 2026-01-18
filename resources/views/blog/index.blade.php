@extends('layouts.app')

@section('content')
<div class="grid grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="col-span-2">
        @forelse($posts as $post)
            <div class="bg-white p-8 mb-8 rounded-lg shadow-sm">
                <h2 class="text-3xl font-bold mb-3">
                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600">
                        {{ $post->title }}
                    </a>
                </h2>
                
                <p class="text-gray-600 text-sm mb-6">
                    Posted on {{ $post->published_at->format('F j, Y') }} by 
                    <span class="text-pink-600 font-semibold">{{ $post->author->display_name }}</span>
                </p>

                <p class="text-gray-700 leading-relaxed mb-6">
                    {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 200) }}
                </p>

                <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-600 hover:text-gray-800 font-semibold">
                    Read More →
                </a>
            </div>
        @empty
            <div class="bg-white p-8 rounded-lg shadow-sm text-center text-gray-600">
                No posts found.
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-span-1">
        <!-- Search -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <h3 class="section-title font-bold text-lg">Search This Blog</h3>
            <form method="GET" action="{{ route('blog.search') }}" class="flex gap-2">
                <input type="text" name="q" placeholder="Search..." class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-blue-400 text-white px-6 py-2 rounded hover:bg-blue-500 font-semibold">
                    Go
                </button>
            </form>
        </div>

        <!-- Categories -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <h3 class="section-title font-bold text-lg">Categories</h3>
            <select class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                <option>Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Archives -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="section-title font-bold text-lg">Archives</h3>
            <select class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                <option>Select Month</option>
                <!-- You can add archive functionality here -->
            </select>
        </div>
    </div>
</div>
@endsection