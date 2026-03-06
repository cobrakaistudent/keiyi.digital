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
        Schema::table('scout_sources', function (Blueprint $table) {
            $table->integer('relevance_score')->default(50)->comment('Puntuación de utilidad 0-100%');
            $table->timestamp('last_crawled_at')->nullable()->comment('Última vez que la IA escarbó la fuente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scout_sources', function (Blueprint $table) {
            $table->dropColumn(['relevance_score', 'last_crawled_at']);
        });
    }
};
