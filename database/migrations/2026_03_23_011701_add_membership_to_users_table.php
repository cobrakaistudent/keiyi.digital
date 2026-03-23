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
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_tier')->default('free')->after('role'); // free, student, general
            $table->decimal('membership_price', 8, 2)->nullable()->after('membership_tier');
            $table->timestamp('membership_started_at')->nullable()->after('membership_price');
            $table->timestamp('membership_expires_at')->nullable()->after('membership_started_at');
            $table->boolean('is_student_verified')->default(false)->after('membership_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_tier', 'membership_price', 'membership_started_at', 'membership_expires_at', 'is_student_verified']);
        });
    }
};
