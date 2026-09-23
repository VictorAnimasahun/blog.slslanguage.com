@extends('admin.layout')

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    /* Toolbar stays on screen while scrolling a long post, instead of only
       being reachable by scrolling back to the very top. */
    .ql-toolbar.ql-snow {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #fff;
    }
</style>
@endpush

@section('title', 'New Post')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.posts.index') }}" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">New Post</h1>
</div>

<form id="post-form" method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main content -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-lg shadow p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                           placeholder="Post title">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="excerpt" rows="2"
                              class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Short summary shown on listing pages">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <div id="quill-editor" class="prose prose-lg max-w-none bg-white" style="min-height: 320px;">{!! old('content') !!}</div>
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
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— No category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image <span class="text-gray-400 font-normal">(optional)</span></label>
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
                    Create Post
                </button>
                <a href="{{ route('admin.posts.index') }}"
                   class="block text-center text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </div>

    </div>
</form>

@include('admin.posts.partials.preview-modal', ['existingFeaturedImageUrl' => null])

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
<script>
    // Drag the handles on an inline image to resize it right in the post.
    Quill.register('modules/imageResize', ImageResize.default);

    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            imageResize: { modules: ['Resize', 'DisplaySize'] },
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

    document.getElementById('post-form').addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });
</script>
@endpush

@endsection
