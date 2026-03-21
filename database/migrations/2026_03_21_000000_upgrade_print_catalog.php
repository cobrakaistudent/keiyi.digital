<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_catalog', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
            $table->string('status')->default('draft')->after('active'); // draft, published
            $table->string('slug')->nullable()->unique()->after('title');
            $table->string('category')->nullable()->after('material'); // figurine, tool, decoration, etc.
        });

        // Allow print_orders without user_id (public quote requests)
        // SQLite doesn't support ALTER COLUMN, so we handle this at app level
    }

    public function down(): void
    {
        Schema::table('print_catalog', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'status', 'slug', 'category']);
        });
    }
};
