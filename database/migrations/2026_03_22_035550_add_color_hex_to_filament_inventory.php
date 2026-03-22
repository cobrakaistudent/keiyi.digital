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
        Schema::table('filament_inventory', function (Blueprint $table) {
            $table->string('color_hex', 7)->nullable()->after('color'); // #FF0000
        });
    }

    public function down(): void
    {
        Schema::table('filament_inventory', function (Blueprint $table) {
            $table->dropColumn('color_hex');
        });
    }
};
