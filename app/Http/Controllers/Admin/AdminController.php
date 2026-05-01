<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Category;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_posts'      => Post::count(),
            'published_posts'  => Post::where('status', 'published')->count(),
            'draft_posts'      => Post::where('status', 'draft')->count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
            'total_comments'   => Comment::count(),
            'total_categories' => Category::count(),
        ];

        $recentPosts    = Post::with('author', 'category')->latest()->take(5)->get();
        $pendingComments = Comment::with('post')->where('status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'pendingComments'));
    }
}
