<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'approved'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas Estáticas de Keiyi Digital 
Route::get('/academy', function () {
    return view('academy');
});

Route::get('/3d-world', function () {
    return view('3d-world');
});

Route::get('/blog', function () {
    return view('blog');
});

// Portal de Alumnos — protegido por auth + approved
Route::prefix('academia')->name('academia.')->middleware(['auth', 'approved'])->group(function () {
    Route::get('/',                   [App\Http\Controllers\AcademiaController::class, 'dashboard'])->name('dashboard');
    Route::post('/enroll/{courseId}', [App\Http\Controllers\AcademiaController::class, 'enroll'])->name('enroll');
});

// Formulario de contacto / cotizacion (publico)
Route::post('/contacto', [App\Http\Controllers\ContactController::class, 'store'])->name('contacto.store');

require __DIR__.'/auth.php';
