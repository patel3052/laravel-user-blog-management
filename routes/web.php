<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\FrontendBlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
    Route::resource('users', UserController::class);

    Route::get('blogs/data', [BlogController::class, 'data'])->name('blogs.data');
    Route::delete('blogs/image/{image}', [BlogController::class, 'destroyImage'])->name('blogs.image.destroy');
    Route::resource('blogs', BlogController::class);
});

Route::get('/blog/{slug}', [FrontendBlogController::class, 'show'])->name('frontend.blog.show');



require __DIR__ . '/auth.php';  
