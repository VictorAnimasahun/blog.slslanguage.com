<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommentVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Comment $comment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm your comment on the SLS Blog',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comment-verification',
        );
    }
}
