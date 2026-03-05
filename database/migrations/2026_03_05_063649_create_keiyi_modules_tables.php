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
        // 1. MÓDULO ACADEMIA: Enrollments
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('course_id');
            $table->integer('progress_percent')->default(0);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();
        });

        // 2. MÓDULO AGENCIA: Clients & Projects
        Schema::create('agency_clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['lead', 'active_client', 'archived'])->default('lead');
            $table->timestamps();
        });

        Schema::create('agency_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->references('id')->on('agency_clients')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('deadline');
            $table->enum('status', ['briefing', 'in_progress', 'delivered'])->default('briefing');
            $table->timestamps();
        });

        // 3. MÓDULO SCOUT AI: Sources & Insights
        Schema::create('scout_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->enum('type', ['rss', 'api', 'sitemap']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('scout_insights', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->json('detected_trends');
            $table->json('recommended_actions');
            $table->text('raw_sources_used');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scout_insights');
        Schema::dropIfExists('scout_sources');
        Schema::dropIfExists('agency_projects');
        Schema::dropIfExists('agency_clients');
        Schema::dropIfExists('enrollments');
    }
};
