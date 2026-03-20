<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo público de modelos 3D
        Schema::create('print_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('embed_url')->nullable();          // Instagram/TikTok embed URL
            $table->string('file_path')->nullable();          // Path al archivo STL/OBJ/3MF
            $table->string('file_name')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('material')->nullable();           // PLA, PETG, Resina, etc.
            $table->string('print_time')->nullable();         // "2-3 horas", "1 día", etc.
            $table->boolean('downloadable')->default(true);
            $table->boolean('orderable')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Órdenes de catálogo y trabajos custom del Taller
        Schema::create('print_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['catalog', 'custom']);
            $table->foreignId('catalog_item_id')->nullable()->constrained('print_catalog')->nullOnDelete();
            $table->string('file_path')->nullable();          // Solo para tipo custom
            $table->string('file_name')->nullable();
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->enum('status', ['received', 'quoting', 'approved', 'printing', 'delivered', 'cancelled'])->default('received');
            $table->text('quote_details')->nullable();        // Respuesta del admin con precio/tiempo
            $table->decimal('quoted_price', 8, 2)->nullable();
            $table->string('quoted_time')->nullable();
            $table->timestamps();
        });

        // Tokens temporales para descarga de archivos
        Schema::create('download_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained('print_catalog')->cascadeOnDelete();
            $table->string('email');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        // Flag de cliente 3D en usuarios
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_3d_client')->default(false)->after('approval_status');
            $table->timestamp('3d_client_approved_at')->nullable()->after('is_3d_client');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_3d_client', '3d_client_approved_at']);
        });
        Schema::dropIfExists('download_tokens');
        Schema::dropIfExists('print_orders');
        Schema::dropIfExists('print_catalog');
    }
};
