<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class CommentVerificationController extends Controller
{
    public function verify(string $token)
    {
        $comment = Comment::where('verification_token', $token)->where('status', 'unverified')->first();

        if (! $comment) {
            return redirect()->route('blog.index')
                ->with('error', 'This confirmation link is invalid or has already been used.');
        }

        $comment->update([
            'status' => 'pending',
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        return redirect()->route('blog.show', $comment->post->slug)
            ->with('success', 'Thanks -- your comment is confirmed and now awaiting moderation.');
    }
}
