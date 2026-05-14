<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();
        $archives   = $this->getArchives();

        return view('blog.index', compact('posts', 'categories', 'archives'));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author', 'category', 'approvedComments.user'])
            ->firstOrFail();

        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->with('author')
            ->latest()
            ->take(5)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::where('category_id', $category->id)
            ->where('status', 'published')
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();
        $archives   = $this->getArchives();

        return view('blog.category', compact('category', 'posts', 'categories', 'archives'));
    }

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
        $archives   = $this->getArchives();

        return view('blog.index', compact('posts', 'categories', 'archives'))
            ->with('searchQuery', $query);
    }

    public function archive($year, $month)
    {
        $posts = Post::where('status', 'published')
            ->whereYear('published_at', $year)
            ->whereMonth('published_at', $month)
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();
        $archives   = $this->getArchives();
        $archiveLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');

        return view('blog.index', compact('posts', 'categories', 'archives'))
            ->with('searchQuery', $archiveLabel);
    }

    public function storeComment(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $validated = $request->validate([
            'content'     => 'required|min:10|max:2000',
            'guest_name'  => 'required_without:user_id|string|max:100|nullable',
            'guest_email' => 'required_without:user_id|email|max:100|nullable',
        ], [
            'content.min'                  => 'Your comment should be at least 10 characters long.',
            'guest_name.required_without'  => 'Please enter your name (required for guests).',
            'guest_email.required_without' => 'Please enter a valid email (required for guests).',
            'guest_email.email'            => 'Please provide a valid email address.',
        ]);

        $post->comments()->create([
            'user_id'     => Auth::id(),
            'guest_name'  => $validated['guest_name']  ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
            'content'     => $validated['content'],
            'status'      => 'pending',
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Thank you! Your comment has been submitted and is awaiting moderation.');
    }

    private function getArchives(): \Illuminate\Support\Collection
    {
        return Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get(['published_at'])
            ->groupBy(fn($p) => $p->published_at->format('Y-m'))
            ->map(fn($posts, $ym) => [
                'label' => $posts->first()->published_at->format('F Y'),
                'year'  => $posts->first()->published_at->format('Y'),
                'month' => $posts->first()->published_at->format('m'),
                'count' => $posts->count(),
            ])
            ->values();
    }
}
