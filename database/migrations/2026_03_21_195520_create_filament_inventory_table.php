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
        Schema::create('filament_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('material');
            $table->string('color');
            $table->integer('weight_grams')->default(1000);
            $table->integer('remaining_grams')->default(1000);
            $table->decimal('cost_per_kg', 8, 2)->default(450.00);
            $table->string('diameter')->default('1.75mm');
            $table->string('status')->default('active');
            $table->date('purchased_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filament_inventory');
    }
};
