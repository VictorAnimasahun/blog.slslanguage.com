<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    $this->post  = Post::factory()->create([
        'author_id'    => $this->admin->id,
        'status'       => 'published',
        'published_at' => now(),
    ]);
    $this->comment = Comment::factory()->create([
        'post_id' => $this->post->id,
        'status'  => 'pending',
    ]);
});

it('admin can view the comments list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.comments.index'))
        ->assertOk();
});

it('admin can approve a comment', function () {
    $this->actingAs($this->admin)
        ->patch(route('admin.comments.approve', $this->comment))
        ->assertRedirect();

    expect($this->comment->fresh()->status)->toBe('approved');
});

it('admin can mark a comment as spam', function () {
    $this->actingAs($this->admin)
        ->patch(route('admin.comments.spam', $this->comment))
        ->assertRedirect();

    expect($this->comment->fresh()->status)->toBe('spam');
});

it('admin can delete a comment', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.comments.destroy', $this->comment))
        ->assertRedirect();

    expect(Comment::find($this->comment->id))->toBeNull();
});
