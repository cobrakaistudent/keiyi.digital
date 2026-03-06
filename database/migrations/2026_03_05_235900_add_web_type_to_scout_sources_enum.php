<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega 'web' al ENUM de scout_sources.type.
     *
     * NOTA TÉCNICA: doctrine/dbal no está instalado en este proyecto,
     * por lo que ->change() no es confiable para modificar ENUMs en MySQL.
     * Se usa DB::statement() con SQL directo, que funciona en cualquier
     * versión de Laravel y PHP sin dependencias adicionales.
     *
     * El guard de driver evita que falle en SQLite local (que no tiene
     * ENUMs y acepta cualquier string, por lo que no necesita el ALTER).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE scout_sources MODIFY COLUMN type ENUM('rss', 'api', 'sitemap', 'web') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE scout_sources MODIFY COLUMN type ENUM('rss', 'api', 'sitemap') NOT NULL");
        }
    }
};
