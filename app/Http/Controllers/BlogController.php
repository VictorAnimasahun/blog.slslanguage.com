<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts (home page).
     */
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    /**
     * Display a single published blog post with related posts and approved comments.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author', 'category', 'approvedComments.user'])
            ->firstOrFail();

        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->with('author')                  // Optional: load author if you display names in related posts
            ->latest()
            ->take(5)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Display posts filtered by category.
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::where('category_id', $category->id)
            ->where('status', 'published')
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('blog.category', compact('category', 'posts'));
    }

    /**
     * Store a new comment for a post (handles both authenticated users and guests).
     */
    public function storeComment(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Optional future check (add a 'allow_comments' boolean column later if needed)
        // if (!$post->allow_comments ?? true) {
        //     return back()->with('error', 'Comments are disabled for this post.');
        // }

        $validated = $request->validate([
            'content'     => 'required|min:10|max:2000',
            'guest_name'  => 'required_without:user_id|string|max:100|nullable',
            'guest_email' => 'required_without:user_id|email|max:100|nullable',
        ], [
            'content.min'               => 'Your comment should be at least 10 characters long.',
            'guest_name.required_without'  => 'Please enter your name (required for guests).',
            'guest_email.required_without' => 'Please enter a valid email (required for guests).',
            'guest_email.email'         => 'Please provide a valid email address.',
        ]);

        // Create the comment using the relationship
        $post->comments()->create([
            'user_id'     => Auth::id(),                        // null if guest
            'guest_name'  => $validated['guest_name']  ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
            'content'     => $validated['content'],
            'status'      => 'pending',
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Thank you! Your comment has been submitted and is awaiting moderation.');
    }

    /**
     * Optional: Search posts (if you want to keep/improve the search form)
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        $posts = Post::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('blog.index', compact('posts', 'categories'))
            ->with('searchQuery', $query);
    }
}