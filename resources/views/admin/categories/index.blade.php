@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Categories</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Create / Edit form -->
    <div class="lg:col-span-1">
        @if(isset($category))
            {{-- Edit mode --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 mb-4">Edit Category</h2>
                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                    @csrf @method('PUT')
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $category->name) }}"
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                            <textarea name="description" rows="3"
                                      class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded text-sm font-medium hover:bg-blue-700">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="block text-center text-sm text-gray-500 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        @else
            {{-- Create mode --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 mb-4">New Category</h2>
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                                   placeholder="e.g. Grammar Tips">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                            <textarea name="description" rows="3"
                                      class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Brief description of this category">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded text-sm font-medium hover:bg-blue-700">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Categories table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="text-left px-5 py-3">Name</th>
                        <th class="text-left px-5 py-3">Slug</th>
                        <th class="text-left px-5 py-3">Posts</th>
                        <th class="text-left px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50 {{ isset($category) && $category->id === $cat->id ? 'bg-blue-50' : '' }}">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $cat->name }}</p>
                            @if($cat->description)
                                <p class="text-gray-400 text-xs truncate max-w-xs">{{ $cat->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400 font-mono text-xs">{{ $cat->slug }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $cat->posts_count }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-3">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                      onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">No categories yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
