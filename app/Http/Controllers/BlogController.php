<?php

namespace App\Http\Controllers;

use App\Mail\CommentVerificationMail;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    /**
     * Public JSON feed of the latest published posts, consumed by the main
     * site's homepage so it only ever shows real posts (it used to carry
     * hardcoded placeholder cards). CORS is limited to the main site's own
     * origins -- this is read-only public data, but there's no reason to
     * offer it to every origin.
     */
    public function latestJson(Request $request)
    {
        $posts = Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->with('author')
            ->orderByDesc('published_at')
            ->limit(min((int) $request->query('limit', 3), 6))
            ->get()
            ->map(fn ($p) => [
                'title'    => $p->title,
                'url'      => route('blog.show', $p->slug),
                'excerpt'  => $p->excerpt ?: \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($p->content))), 160),
                'date'     => $p->published_at->format('F j, Y'),
                'author'   => $p->author->display_name ?? null,
                'image'    => $p->featured_image ? url(\Illuminate\Support\Facades\Storage::url($p->featured_image)) : null,
            ]);

        $origin = $request->headers->get('Origin');
        $allowed = ['https://slslanguage.com', 'https://www.slslanguage.com', 'http://localhost:8888', 'http://localhost'];

        return response()->json(['posts' => $posts])
            ->header('Access-Control-Allow-Origin', in_array($origin, $allowed, true) ? $origin : 'https://slslanguage.com')
            ->header('Vary', 'Origin')
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function storeComment(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // required_without:user_id used to be checked against a 'user_id' REQUEST
        // field -- which the form never sends (it's derived from Auth::id(), not
        // submitted by the client) -- so guest_name/guest_email were "required"
        // even when logged in, where the form doesn't show those fields at all.
        // Logged-in comments have silently failed validation ever since (caught
        // while testing the new guest-verification flow below). Checking the
        // actual auth state instead of a request field fixes both paths.
        $guestRule = Auth::check() ? 'nullable' : 'required';
        $validated = $request->validate([
            'content'     => 'required|min:10|max:2000',
            'guest_name'  => "$guestRule|string|max:100",
            'guest_email' => "$guestRule|email|max:100",
        ], [
            'content.min'          => 'Your comment should be at least 10 characters long.',
            'guest_name.required'  => 'Please enter your name.',
            'guest_email.required' => 'Please enter a valid email.',
            'guest_email.email'    => 'Please provide a valid email address.',
        ]);

        // Logged-in users already have a real account on file -- only guests
        // need their email confirmed before a comment enters the moderation
        // queue. A guest comment starts 'unverified' with no expiry on the
        // token; it simply never becomes visible until the link is clicked.
        if (Auth::check()) {
            $post->comments()->create([
                'user_id'            => Auth::id(),
                'content'            => $validated['content'],
                'status'             => 'pending',
                'email_verified_at'  => now(),
                'ip_address'         => $request->ip(),
            ]);

            return back()->with('success', 'Thank you! Your comment has been submitted and is awaiting moderation.');
        }

        $comment = $post->comments()->create([
            'guest_name'          => $validated['guest_name'],
            'guest_email'         => $validated['guest_email'],
            'content'             => $validated['content'],
            'status'              => 'unverified',
            'verification_token'  => Str::random(40),
            'ip_address'          => $request->ip(),
        ]);

        Mail::to($comment->guest_email)->send(new CommentVerificationMail($comment));

        return back()->with('success', 'Almost done -- check your email and click the confirmation link to submit your comment for review.');
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
