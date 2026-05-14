<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'status' => 'active']);
}

function makeEditor(): User
{
    return User::factory()->create(['role' => 'editor', 'status' => 'active']);
}

// --- Access control ---

it('redirects guests away from admin', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

it('blocks regular users from admin', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
});

it('allows admins to access the dashboard', function () {
    $this->actingAs(makeAdmin())->get(route('admin.dashboard'))->assertOk();
});

it('allows editors to access the dashboard', function () {
    $this->actingAs(makeEditor())->get(route('admin.dashboard'))->assertOk();
});

// --- Post CRUD ---

it('admin can create a post', function () {
    $admin = makeAdmin();
    $cat   = Category::factory()->create();

    $this->actingAs($admin)->post(route('admin.posts.store'), [
        'title'       => 'My First Post',
        'content'     => 'Some content here for the post body.',
        'status'      => 'draft',
        'category_id' => $cat->id,
    ])->assertRedirect(route('admin.posts.index'));

    expect(Post::where('title', 'My First Post')->exists())->toBeTrue();
});

it('editor can create a post', function () {
    $editor = makeEditor();

    $this->actingAs($editor)->post(route('admin.posts.store'), [
        'title'   => 'Editor Post',
        'content' => 'Content written by editor.',
        'status'  => 'draft',
    ])->assertRedirect(route('admin.posts.index'));

    expect(Post::where('author_id', $editor->id)->exists())->toBeTrue();
});

it('editor cannot edit another author\'s post', function () {
    $editor = makeEditor();
    $admin  = makeAdmin();
    $post   = Post::factory()->create(['author_id' => $admin->id, 'status' => 'draft']);

    $this->actingAs($editor)
        ->get(route('admin.posts.edit', $post))
        ->assertForbidden();
});

it('editor can edit their own post', function () {
    $editor = makeEditor();
    $post   = Post::factory()->create(['author_id' => $editor->id, 'status' => 'draft']);

    $this->actingAs($editor)
        ->get(route('admin.posts.edit', $post))
        ->assertOk();
});

it('admin can delete any post', function () {
    $admin = makeAdmin();
    $post  = Post::factory()->create(['author_id' => $admin->id]);

    $this->actingAs($admin)
        ->delete(route('admin.posts.destroy', $post))
        ->assertRedirect(route('admin.posts.index'));

    expect(Post::find($post->id))->toBeNull();
});

// --- Admin-only routes ---

it('editor cannot access user management', function () {
    $this->actingAs(makeEditor())
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

it('editor cannot access category management', function () {
    $this->actingAs(makeEditor())
        ->get(route('admin.categories.index'))
        ->assertForbidden();
});
