<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PostController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/3d-world', function () {
    return view('3d-world');
})->name('3d-world');

// Lista de blogs
Route::get('/blog', function () {
    $posts = Post::where('is_published', true)->latest()->get();
    return view('blog', compact('posts'));
})->name('blog');

// Artículo individual (DINÁMICO)
Route::get('/blog/{slug}', function ($slug) {
    $post = Post::where('slug', $slug)->firstOrFail(); // Si no existe, da error 404
    return view('blog-show', compact('post'));
})->name('blog.show');

// Admin Routes (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Blog Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('posts', PostController::class);
});

// Academy Routes (Para Alumnos)
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckApproved::class])->prefix('academy')->name('academy.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AcademyController::class, 'dashboard'])->name('dashboard');
    Route::get('/resource/{filename}', [\App\Http\Controllers\AcademyController::class, 'downloadResource'])
         ->name('resource.download');
    Route::get('/{courseSlug}/{lessonNumber}/{type?}', [\App\Http\Controllers\AcademyController::class, 'showLesson'])
         ->name('lesson')
         ->where(['lessonNumber' => '[0-9]+', 'type' => 'guia|script']);
});

// Admin Academy Management
Route::middleware(['auth', 'verified'])->prefix('admin/academy')->name('admin.academy.')->group(function () {
    Route::get('/students', [\App\Http\Controllers\AcademyController::class, 'manageStudents'])->name('students');
    Route::post('/approve/{id}', [\App\Http\Controllers\AcademyController::class, 'approveStudent'])->name('approve');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
