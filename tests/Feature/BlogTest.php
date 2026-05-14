<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

// --- Public blog routes ---

it('shows the blog index', function () {
    $this->get(route('blog.index'))->assertOk();
});

it('shows a published post', function () {
    $user = User::factory()->create();
    $cat  = Category::factory()->create();
    $post = Post::factory()->create([
        'status'       => 'published',
        'published_at' => now(),
        'author_id'    => $user->id,
        'category_id'  => $cat->id,
    ]);

    $this->get(route('blog.show', $post->slug))->assertOk()->assertSee($post->title);
});

it('returns 404 for a draft post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status'    => 'draft',
        'author_id' => $user->id,
    ]);

    $this->get(route('blog.show', $post->slug))->assertNotFound();
});

it('filters posts by category', function () {
    $user = User::factory()->create();
    $cat  = Category::factory()->create();
    Post::factory()->create([
        'status'       => 'published',
        'published_at' => now(),
        'author_id'    => $user->id,
        'category_id'  => $cat->id,
    ]);

    $this->get(route('blog.category', $cat->slug))->assertOk()->assertSee($cat->name);
});

it('searches posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status'       => 'published',
        'published_at' => now(),
        'author_id'    => $user->id,
        'title'        => 'Unique IELTS Writing Tips',
    ]);

    $this->get(route('blog.search', ['q' => 'IELTS']))->assertOk()->assertSee($post->title);
});

it('archives posts by month and year', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status'       => 'published',
        'published_at' => now(),
        'author_id'    => $user->id,
    ]);

    $this->get(route('blog.archive', [
        now()->year,
        now()->format('m'),
    ]))->assertOk()->assertSee($post->title);
});

// --- Comments ---

it('allows a guest to submit a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status'       => 'published',
        'published_at' => now(),
        'author_id'    => $user->id,
    ]);

    $this->post(route('blog.comment.store', $post->slug), [
        'content'     => 'This is a great post, thank you!',
        'guest_name'  => 'Jane Doe',
        'guest_email' => 'jane@example.com',
    ])->assertRedirect();

    expect(Comment::where('post_id', $post->id)->count())->toBe(1);
    expect(Comment::first()->status)->toBe('pending');
});

it('validates comment content length', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'status'       => 'published',
        'published_at' => now(),
        'author_id'    => $user->id,
    ]);

    $this->post(route('blog.comment.store', $post->slug), [
        'content'     => 'Too short',
        'guest_name'  => 'Jane',
        'guest_email' => 'jane@example.com',
    ])->assertSessionHasErrors('content');
});
