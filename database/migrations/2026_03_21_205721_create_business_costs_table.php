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
        // Costos fijos y variables del negocio
        Schema::create('business_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                              // "Hostinger", "Claude Pro", "Gemini", "Luz", etc.
            $table->string('category');                          // hosting, ai_tools, electricity, development, other
            $table->decimal('amount', 10, 2);                    // Costo en MXN
            $table->string('currency')->default('MXN');
            $table->string('frequency');                         // monthly, yearly, one_time, per_kwh
            $table->text('notes')->nullable();
            $table->string('url')->nullable();                   // Link de compra/proveedor
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Configuración de pricing para impresión 3D
        Schema::create('pricing_config', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label');
            $table->string('unit')->nullable();                  // MXN, watts, %, hrs, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_config');
        Schema::dropIfExists('business_costs');
    }
};
