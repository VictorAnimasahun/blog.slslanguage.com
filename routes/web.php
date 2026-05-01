<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');

require __DIR__.'/auth.php';

Route::post('/blog/{slug}/comment', [BlogController::class, 'storeComment'])
    ->name('blog.comment.store')
    ->middleware('throttle:5,1');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('posts', Admin\PostController::class);

    Route::get('comments', [Admin\CommentController::class, 'index'])->name('comments.index');
    Route::patch('comments/{comment}/approve', [Admin\CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('comments/{comment}/spam', [Admin\CommentController::class, 'spam'])->name('comments.spam');
    Route::delete('comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');
});