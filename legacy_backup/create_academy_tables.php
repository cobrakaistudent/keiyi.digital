<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de Cursos
        Schema::create('academy_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->string('level'); // Inicial, Intermedio, Elite
            $table->timestamps();
        });

        // Tabla de Lecciones
        Schema::create('academy_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable(); // Contenido MD o HTML
            $table->string('video_url')->nullable(); // Vimeo/YouTube
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Inscripciones (Relación Usuario -> Curso)
        Schema::create('academy_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('academy_course_id')->constrained()->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_enrollments');
        Schema::dropIfExists('academy_lessons');
        Schema::dropIfExists('academy_courses');
    }
};
