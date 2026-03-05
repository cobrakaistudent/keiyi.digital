# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Keiyi Digital** is a Laravel 11 application serving as a business platform for a digital agency + EdTech. It has three functional layers:
1. **Public frontend** - Marketing pages (welcome, academy, blog, 3d-world) with legacy CSS/JS served from `/public`
2. **Auth layer** - Laravel Breeze (Blade stack) with a custom `approved` middleware gate
3. **Admin CRM** - Filament 3 panel at `/admin` for managing clients, projects, and Scout AI data

## Stack

- **PHP:** 8.3 | **Laravel:** 11.47 | **Filament:** 3.x | **Breeze:** 2.x (Blade)
- **Local DB:** SQLite (`database/database.sqlite`) | **Production DB:** MySQL (Hostinger)
- **Frontend assets:** Vite + legacy `public/style.css` + `public/script.js`
- **AI Agent:** Python script (`agent/scout.py`) running on local Mac M2 via Ollama

## Common Commands

```bash
# Development (runs server, queue, logs, and Vite in parallel)
composer dev

# Or individually
php artisan serve
npm run dev

# Migrations
php artisan migrate

# Linting
./vendor/bin/pint

# Tests
php artisan test
php artisan test --filter=FeatureNameTest

# Filament panel upgrade (run after composer update)
php artisan filament:upgrade
```

## Architecture

### User Flow & Authentication
- Registration creates users with `approval_status = null`
- The `approved` middleware alias (`CheckApproved`) blocks unapproved users from accessing `/dashboard`
- Admin manually sets `approval_status = 'approved'` via the Filament `UserResource`
- Filament admin at `/admin` has its own auth separate from the Breeze login at `/login`

### Database Modules (all in one migration: `2026_03_05..._create_keiyi_modules_tables.php`)
- **Academy:** `enrollments` (user_id, course_id, progress_percent)
- **Agency:** `agency_clients` (leads/active/archived) + `agency_projects` (briefing/in_progress/delivered)
- **Scout AI:** `scout_sources` (URL watchlist) + `scout_insights` (AI-generated reports as JSON)

### Scout AI System (Hybrid Architecture)
The AI intelligence pipeline is a client-server bridge:
- **Server side (Laravel):** `ScoutApiController` exposes two Sanctum-protected endpoints:
  - `GET /api/scout/pending` - returns active `ScoutSource` records
  - `POST /api/scout/insight` - receives AI report and stores it as `ScoutInsight`
- **Client side (Mac M2):** `agent/scout.py` runs locally, scrapes RSS feeds, sends context to Ollama (`qwen3:8b`), and POSTs the JSON result to the Laravel API
- Authentication: Sanctum token stored in `agent/scout.py` as `SANCTUM_TOKEN` (must be generated from admin panel and set manually)

### Filament Resources (`app/Filament/Resources/`)
- `UserResource` - user management with approval workflow
- `AgencyClientResource` - CRM for agency clients
- `ScoutSourceResource` - manage URLs for the Scout AI watchlist
- Admin panel configured in `app/Providers/Filament/AdminPanelProvider.php`

## Deployment Context (Hostinger Shared Hosting)

- **No sudo access** - cannot install system binaries (Redis, Supervisor, etc.)
- **Web root:** `public_html` - the Laravel `public/` folder contents must be symlinked or copied there; `laravel_app/` lives one level above for security
- **Scheduled tasks:** Use Laravel's `Schedule` only via Hostinger's Cron panel
- **Config changes:** Only via `.htaccess`, no Apache/Nginx direct access
- **The Python scout agent runs locally on Mac M2**, not on the server

## Key Files

| File | Purpose |
|------|---------|
| `AGENT_COMMUNICATION.md` | Inter-agent communication log between Antigravity (orchestrator) and Gemini CLI (engineer) |
| `agent/scout.py` | Local Python AI agent - configure `SANCTUM_TOKEN` and `LARAVEL_API_BASE` before running |
| `app/Http/Middleware/CheckApproved.php` | Blocks unapproved users from protected routes |
| `bootstrap/app.php` | Middleware alias registration (`approved`) |
