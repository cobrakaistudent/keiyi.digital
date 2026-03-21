<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('academia.dashboard');
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

// 3D World — Galería pública
Route::prefix('3d-world')->name('world3d.')->group(function () {
    Route::get('/',                   [App\Http\Controllers\World3DController::class, 'index'])->name('index');
    Route::post('/download/{item}',   [App\Http\Controllers\World3DController::class, 'requestDownload'])->name('request_download');
    Route::get('/download/{token}',   [App\Http\Controllers\World3DController::class, 'download'])->name('download');
    Route::post('/order/{item}',      [App\Http\Controllers\World3DController::class, 'requestOrder'])->name('order');
});

// El Taller — zona privada para clientes 3D aprobados
Route::prefix('taller')->name('taller.')->group(function () {
    Route::get('/registro',  [App\Http\Controllers\TallerController::class, 'registro'])->name('registro');
    Route::post('/registro', [App\Http\Controllers\TallerController::class, 'storeRegistro'])->name('registro.store');

    Route::middleware(['auth', '3d_client'])->group(function () {
        Route::get('/',         [App\Http\Controllers\TallerController::class, 'index'])->name('index');
        Route::post('/upload',  [App\Http\Controllers\TallerController::class, 'upload'])->name('upload');
    });
});

// Blog público de Keiyi
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/',         [App\Http\Controllers\BlogController::class, 'index'])->name('index');
    Route::get('/{slug}',   [App\Http\Controllers\BlogController::class, 'show'])->name('show');
});

// Portal de Alumnos — protegido por auth + approved
Route::prefix('academia')->name('academia.')->middleware(['auth', 'approved'])->group(function () {
    Route::get('/',                   [App\Http\Controllers\AcademiaController::class, 'dashboard'])->name('dashboard');
    Route::post('/enroll/{courseId}', [App\Http\Controllers\AcademiaController::class, 'enroll'])->name('enroll');

    // LMS — Cursos y lecciones
    Route::get('/curso/{courseSlug}',                          [App\Http\Controllers\CourseController::class, 'show'])->name('curso');
    Route::get('/curso/{courseSlug}/{lessonSlug}',              [App\Http\Controllers\CourseController::class, 'lessonShow'])->name('curso.leccion');
    Route::post('/curso/{courseSlug}/{lessonSlug}/complete',    [App\Http\Controllers\CourseController::class, 'markComplete'])->name('curso.complete');
    Route::post('/curso/{courseSlug}/{lessonSlug}/quiz',        [App\Http\Controllers\CourseController::class, 'submitQuiz'])->name('curso.quiz');
});

// Formulario de contacto / cotizacion (publico)
Route::post('/contacto', [App\Http\Controllers\ContactController::class, 'store'])->name('contacto.store');

require __DIR__.'/auth.php';
