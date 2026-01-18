<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with('author', 'category', 'comments')
            ->firstOrFail();

        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->limit(3)
            ->get();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::where('category_id', $category->id)
            ->where('status', 'published')
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('blog.category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
