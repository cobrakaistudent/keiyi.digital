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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('description');
            $table->string('category');          // hosting, ai_tools, filament, electricity, equipment, software, marketing, legal, development, other
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('MXN');
            $table->decimal('amount_mxn', 10, 2); // monto convertido al momento del registro
            $table->string('payment_method')->nullable(); // transfer, card, cash, paypal
            $table->string('vendor')->nullable();  // proveedor
            $table->string('receipt_url')->nullable(); // link a comprobante
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
