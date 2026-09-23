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

<form id="post-form" method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
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
                    <div id="quill-editor" class="prose prose-lg max-w-none bg-white" style="min-height: 320px;"
                         data-content="{{ json_encode(old('content', $post->content)) }}"></div>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                    @if($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" alt="Current featured image"
                             class="w-full h-32 object-cover rounded mb-2">
                        <p class="text-xs text-gray-400 mb-2">Upload a new image to replace the current one.</p>
                    @endif
                    <input type="file" name="featured_image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('featured_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="button" onclick="openPreview()"
                        class="w-full border border-blue-300 text-blue-600 py-2 rounded text-sm font-medium hover:bg-blue-50">
                    <i class="fas fa-eye mr-1"></i> Preview
                </button>
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
                <!-- Not a real <form> here on purpose: this sits inside the "Save Changes"
                     form above (post-form), and a form nested inside another form is invalid
                     HTML. Browsers "fix" that by dropping the inner <form> tag but keeping its
                     hidden inputs as siblings inside the outer one -- so a click on "Save
                     Changes" was also submitting this block's own hidden @method('DELETE')
                     field, and PHP's "last value with the same name wins" rule meant the
                     PUT above got silently overridden by that DELETE, deleting the post
                     instead of updating it. (Reproduced and confirmed 2026-09-23.) Submitting
                     a form built and appended to <body> in JS can never nest, so it's safe. -->
                <button type="button" onclick="deletePost()"
                        class="w-full border border-red-300 text-red-500 py-2 rounded text-sm hover:bg-red-50">
                    Delete Post
                </button>
            </div>
        </div>

    </div>
</form>

@include('admin.posts.partials.preview-modal', [
    'existingFeaturedImageUrl' => $post->featured_image ? Storage::url($post->featured_image) : null,
])

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: {
                container: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ],
                handlers: {
                    image: function () {
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        input.click();
                        input.onchange = () => {
                            const file = input.files[0];
                            if (!file) return;
                            // Upload the file (see PostController::uploadImage) and embed
                            // the returned URL -- NOT the file itself as base64. A base64
                            // embed used to be able to blow past post_max_size on its own,
                            // which failed the whole save with no visible explanation.
                            const range = quill.getSelection(true);
                            const placeholderId = 'img-upload-' + Date.now();
                            const placeholderSvg = 'data:image/svg+xml;utf8,' + encodeURIComponent(
                                '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="120"><rect width="200" height="120" fill="#f1f5f9"/><text x="100" y="64" font-size="13" text-anchor="middle" fill="#94a3b8" font-family="sans-serif">Uploading…</text></svg>'
                            );
                            quill.insertEmbed(range.index, 'image', placeholderSvg);
                            quill.formatText(range.index, 1, { alt: placeholderId });
                            quill.setSelection(range.index + 1);

                            const formData = new FormData();
                            formData.append('image', file);
                            formData.append('_token', document.querySelector('input[name="_token"]').value);

                            fetch('{{ route('admin.posts.upload-image') }}', { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } })
                                .then(res => {
                                    if (!res.ok) return res.json().then(err => { throw err; });
                                    return res.json();
                                })
                                .then(data => {
                                    const img = quill.root.querySelector(`img[alt="${placeholderId}"]`);
                                    if (img) { img.src = data.url; img.removeAttribute('alt'); }
                                })
                                .catch(err => {
                                    const img = quill.root.querySelector(`img[alt="${placeholderId}"]`);
                                    if (img) img.remove();
                                    const msg = err?.errors?.image?.[0] || 'Could not upload that image. Try a smaller file.';
                                    alert(msg);
                                });
                        };
                    }
                }
            }
        }
    });

    const editorEl = document.getElementById('quill-editor');
    const savedContent = editorEl.getAttribute('data-content');
    quill.root.innerHTML = savedContent ? JSON.parse(savedContent) : '';

    document.getElementById('post-form').addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });

    // Builds and submits its own detached form (never a descendant of post-form,
    // so it can never repeat the nested-form bug described above).
    function deletePost() {
        if (!confirm('Permanently delete this post and all its comments?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.posts.destroy', $post) }}';
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush

@endsection
