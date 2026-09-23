<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: -apple-system, Arial, sans-serif; color: #1f2937; max-width: 560px; margin: 0 auto; padding: 24px;">
    <h2 style="color: #111827;">Confirm your comment</h2>

    <p>Hi {{ $comment->guest_name }},</p>

    <p>You left a comment on <strong>{{ $comment->post->title }}</strong> on the SLS Blog. Click below to confirm
        it's really you -- once confirmed, your comment goes to the moderation queue for review.</p>

    <blockquote style="border-left: 3px solid #d1d5db; margin: 16px 0; padding: 8px 16px; color: #4b5563;">
        {{ $comment->content }}
    </blockquote>

    <p style="margin: 24px 0;">
        <a href="{{ route('comments.verify', $comment->verification_token) }}"
           style="background: #3b82f6; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            Confirm my comment
        </a>
    </p>

    <p style="color: #6b7280; font-size: 13px;">
        If you didn't leave this comment, you can ignore this email -- it won't be posted without confirmation.
    </p>
</body>
</html>
