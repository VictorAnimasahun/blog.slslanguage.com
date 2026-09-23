<!--
    "See exactly how it will look before posting." Deliberately outside
    #post-form -- it's a plain <div>, never a <form>, so it can't repeat the
    nested-form bug even by accident. Reuses the exact markup + classes from
    blog/show.blade.php's article header, and the SAME .prose CSS the
    published page uses (enabled here via admin/layout.blade.php's
    ?plugins=typography + the shared spacing override), so this is a real
    preview, not an approximation -- if it looks right here, it looks right
    to a reader.

    Expects, from the including view:
      $existingFeaturedImageUrl -- Storage::url() of the already-saved image, or null (create page)
-->
<div id="preview-overlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-start justify-center overflow-y-auto py-8" onclick="if(event.target===this) closePreview()">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4">
        <div class="flex items-center justify-between px-6 py-3 border-b bg-gray-50 rounded-t-lg">
            <span class="text-sm font-semibold text-gray-600">
                <i class="fas fa-eye mr-1"></i> Preview — this is what a reader will see
            </span>
            <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div class="overflow-hidden rounded-b-lg">
            <img id="preview-image" src="" alt="" class="w-full h-72 object-cover hidden">
            <div class="p-8">
                <h1 id="preview-title" class="text-4xl font-bold mb-4"></h1>
                <p class="text-gray-600 text-sm mb-8">
                    Posted on <span id="preview-date"></span> by
                    <span class="text-pink-600 font-semibold">{{ auth()->user()->display_name ?? auth()->user()->first_name }}</span>
                    in <span id="preview-category" class="text-blue-600"></span>
                </p>
                <div id="preview-content" class="prose prose-lg max-w-none"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openPreview() {
        const title = document.querySelector('input[name="title"]').value.trim();
        document.getElementById('preview-title').textContent = title || '(Untitled)';

        const categorySelect = document.querySelector('select[name="category_id"]');
        const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text || 'Uncategorized';
        document.getElementById('preview-category').textContent = categoryText.trim() === '— No category —' ? 'Uncategorized' : categoryText;

        document.getElementById('preview-date').textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

        document.getElementById('preview-content').innerHTML = quill.root.innerHTML;

        const imageInput = document.querySelector('input[name="featured_image"]');
        const previewImg = document.getElementById('preview-image');
        const newFile = imageInput?.files?.[0];
        const existingUrl = @json($existingFeaturedImageUrl ?? null);
        if (newFile) {
            previewImg.src = URL.createObjectURL(newFile);
            previewImg.classList.remove('hidden');
        } else if (existingUrl) {
            previewImg.src = existingUrl;
            previewImg.classList.remove('hidden');
        } else {
            previewImg.classList.add('hidden');
        }

        document.getElementById('preview-overlay').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('preview-overlay').classList.add('hidden');
    }
</script>
