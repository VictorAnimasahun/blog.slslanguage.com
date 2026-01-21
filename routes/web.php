<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/search', [BlogController::class, 'search'])->name('blog.search');

Route::post('/blog/{slug}/comment', [BlogController::class, 'storeComment'])
    ->name('blog.comment.store')
    ->middleware('throttle:5,1');   // Optional: limit to 5 comments per minute per IP