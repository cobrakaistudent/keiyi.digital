<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Certificados de finalización de curso
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique(); // Código alfanumérico verificable
            $table->string('student_name');        // Nombre completo al momento de emisión
            $table->string('course_title');         // Título del curso al momento de emisión
            $table->integer('score')->nullable();   // Promedio de quizzes si aplica
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete(); // Instructor o admin que validó
            $table->timestamps();
        });

        // Grupos de instructor
        Schema::create('course_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // "Marketing Q1 2026", "Equipo Ventas"
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, completed, archived
            $table->timestamps();
        });

        // Relación grupo ↔ alumnos
        Schema::create('course_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('course_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('enrolled'); // enrolled, completed, dropped
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_group_members');
        Schema::dropIfExists('course_groups');
        Schema::dropIfExists('certificates');
    }
};
