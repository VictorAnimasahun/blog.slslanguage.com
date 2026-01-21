@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Mobile: Show sidebar at top, Desktop: Grid layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content - Full width on mobile, 2 cols on desktop -->
        <div class="lg:col-span-2 order-2 lg:order-1">
            @forelse($posts as $post)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 hover:shadow-lg transition-shadow">
                    <h2 class="text-xl md:text-2xl font-bold mb-3">
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-800 hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    
                    <div class="flex flex-wrap gap-2 md:gap-4 text-xs md:text-sm text-gray-600 mb-4">
                        <span><i class="far fa-calendar"></i> {{ $post->published_at->format('M d, Y') }}</span>
                        <span><i class="far fa-user"></i> {{ $post->author->display_name }}</span>
                    </div>
                    
                    <p class="text-sm md:text-base text-gray-700 mb-4 leading-relaxed">
                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 200) }}
                    </p>
                    
                    <a href="{{ route('blog.show', $post->slug) }}" 
                       class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors text-sm md:text-base">
                        Read More <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @empty
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <p class="text-yellow-700">No posts found.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar - Full width on mobile (shows first), 1 col on desktop -->
        <div class="lg:col-span-1 order-1 lg:order-2">
            
            <!-- Search Widget -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
                <h3 class="section-title text-lg md:text-xl font-bold">Search</h3>
                <form action="{{ route('blog.search') }}" method="GET" class="flex gap-2">
                    <input type="text" 
                           name="q" 
                           placeholder="Search posts..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors text-sm md:text-base whitespace-nowrap">
                        Go
                    </button>
                </form>
            </div>

            <!-- Categories Widget -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6">
                <h3 class="section-title text-lg md:text-xl font-bold">Categories</h3>
                <select class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                    <option>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Archives Widget -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                <h3 class="section-title text-lg md:text-xl font-bold">Archives</h3>
                <select class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                    <option>Select Month</option>
                    <option>December 2024</option>
                    <option>November 2024</option>
                    <option>October 2024</option>
                </select>
            </div>

        </div>
    </div>
</div>
@endsection