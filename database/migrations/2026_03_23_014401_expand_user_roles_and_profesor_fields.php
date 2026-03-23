<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Expand role ENUM: add 'teacher' and 'client'
        if ($driver === 'sqlite') {
            // SQLite: rename column trick (no ALTER ENUM)
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role', 'role_old');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('student')->after('email');
            });
            DB::table('users')->update(['role' => DB::raw('role_old')]);
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_old');
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super-admin','student','teacher','client') DEFAULT 'student'");
        }

        // Profesor and client fields
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('student_limit')->nullable()->after('is_student_verified');
            $table->foreignId('profesor_id')->nullable()->after('student_limit')
                ->constrained('users')->nullOnDelete();
            $table->string('company_name')->nullable()->after('profesor_id');
            $table->string('phone', 20)->nullable()->after('company_name');
        });

        // Track who enrolled a student (self vs profesor)
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('enrolled_by')->nullable()->after('enrolled_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('enrolled_by');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profesor_id');
            $table->dropColumn(['student_limit', 'company_name', 'phone']);
        });

        $driver = DB::getDriverName();
        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super-admin','student') DEFAULT 'student'");
        }
    }
};
