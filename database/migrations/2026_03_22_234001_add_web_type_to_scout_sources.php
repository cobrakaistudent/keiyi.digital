<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN for enums — recreate column
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('scout_sources', function (Blueprint $table) {
                $table->string('type_new')->default('rss');
            });

            DB::table('scout_sources')->update(['type_new' => DB::raw('type')]);

            Schema::table('scout_sources', function (Blueprint $table) {
                $table->dropColumn('type');
            });

            Schema::table('scout_sources', function (Blueprint $table) {
                $table->enum('type', ['rss', 'web', 'api', 'sitemap'])->default('rss');
            });

            DB::table('scout_sources')->update(['type' => DB::raw('type_new')]);

            Schema::table('scout_sources', function (Blueprint $table) {
                $table->dropColumn('type_new');
            });
        } else {
            // MySQL: ALTER COLUMN directly
            DB::statement("ALTER TABLE scout_sources MODIFY COLUMN type ENUM('rss', 'web', 'api', 'sitemap') NOT NULL DEFAULT 'rss'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Revert would lose 'web' rows — skip for safety
        } else {
            DB::statement("ALTER TABLE scout_sources MODIFY COLUMN type ENUM('rss', 'api', 'sitemap') NOT NULL DEFAULT 'rss'");
        }
    }
};
