<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $comments = Comment::with('post', 'user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25);

        $counts = [
            'pending'  => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'spam'     => Comment::where('status', 'spam')->count(),
            'all'      => Comment::count(),
        ];

        return view('admin.comments.index', compact('comments', 'status', 'counts'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Comment approved.');
    }

    public function spam(Comment $comment)
    {
        $comment->update(['status' => 'spam']);
        return back()->with('success', 'Comment marked as spam.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
