@extends('admin.layout')

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

@section('title', 'Edit Post')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.posts.index') }}" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Edit Post</h1>
</div>

<form method="POST" action="{{ route('admin.posts.update', $post) }}">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main content -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-lg shadow p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-1">Slug: <span class="font-mono">{{ $post->slug }}</span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="excerpt" rows="2"
                              class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <div id="quill-editor" class="bg-white" style="min-height: 320px;"></div>
                    <textarea name="content" id="content-input" class="hidden @error('content') border-red-400 @enderror"></textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— No category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded text-sm font-medium hover:bg-blue-700">
                    Save Changes
                </button>

                @if($post->status === 'published')
                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                       class="block text-center text-sm text-blue-600 hover:underline">
                        View post →
                    </a>
                @endif

                <a href="{{ route('admin.posts.index') }}"
                   class="block text-center text-sm text-gray-500 hover:underline">Cancel</a>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs text-gray-400 mb-3">Danger zone</p>
                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                      onsubmit="return confirm('Permanently delete this post and all its comments?')">
                    @csrf @method('DELETE')
                    <button class="w-full border border-red-300 text-red-500 py-2 rounded text-sm hover:bg-red-50">
                        Delete Post
                    </button>
                </form>
            </div>
        </div>

    </div>
</form>
@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });

    quill.root.innerHTML = {!! json_encode(old('content', $post->content)) !!};

    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });
</script>
@endpush

@endsection
