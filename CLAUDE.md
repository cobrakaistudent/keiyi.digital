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
- **AI Agents:** Perry/Dipper/William Python agents on local Mac M2 via Ollama (`gemma3:4b`)
- **Native App:** `agent/KeiyiAgent.swift` — macOS command center (build with `bash agent/build_agent.sh`)

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

### Agent Pipeline (Mac M2 → Ollama/Gemini)
| Agent | Script | Model | Purpose |
|---|---|---|---|
| **Perry** | `agent/perry.py` | Gemini CLI | Reconnaissance — scrapes sources, discovers communities |
| **Dipper** | `agent/dipper_scout.py` | `keiyi-dipper` (Ollama) | Intelligence — extracts tools/questions/references from scraped data |
| **William** | `agent/william.py` | `keiyi-william` (Ollama) | Blog writer — generates drafts from research_db, validates quality |

- **Auto-run:** `agent/idle_config.json` — daily 5-7 AM via Swift AppDelegate
- **Modelfiles:** `agent/william.modelfile`, `agent/dipper.modelfile` (both `FROM gemma3:4b`)
- **Data flow:** Perry scrapes → `hot_sources.json` → Dipper extracts → `research_db.json` → William writes → `william_drafts/`
- **Editorial review:** `http://localhost:4000/demo_william.html` — feedback system with comments API, saved to `agent/william_feedback.json`

### LMS / Academia
- **Tables:** `courses`, `lessons`, `lesson_completions` (migration: `2026_03_16_000000_create_lms_tables.php`)
- **Models:** `Course`, `Lesson`, `LessonCompletion`, `Enrollment`
- **Lesson types:** `lecture`, `quiz`, `interactive` — quiz scoring with configurable `pass_threshold`
- **Seeders:** `CourseSeeder`, `Taller0Seeder`, `Taller1Seeder`, `Taller2Seeder`, `MarketingEliteSeeder`
- 5 published courses, 48 lessons total

### Filament Resources (`app/Filament/Resources/`)
- `UserResource` - user management with approval workflow
- `AgencyClientResource` - CRM for agency clients
- `CourseResource` - course + lesson management with `LessonsRelationManager`
- `PostResource` - blog post management with approval workflow
- `ContactMessageResource`, `PrintCatalogResource`, `PrintOrderResource`
- Admin panel configured in `app/Providers/Filament/AdminPanelProvider.php`

## Deployment (Hostinger Shared Hosting)

- **No sudo access** - cannot install system binaries (Redis, Supervisor, etc.)
- **Web root:** `public_html` — Laravel `public/` contents synced here; `laravel_app/` lives one level above
- **Scheduled tasks:** Use Laravel's `Schedule` only via Hostinger's Cron panel
- **Config changes:** Only via `.htaccess`, no Apache/Nginx direct access
- **SSH:** `ssh -p 65002 -i ~/.ssh/id_rsa u129237724@185.212.70.24`
- **Python on Mac:** `/Library/Frameworks/Python.framework/Versions/3.11/bin/python3` (NOT `/usr/bin/python3`)

### Deploy Rules (CRITICAL)
- **NEVER rsync `public/index.php` to `public_html/`** — production index.php has different paths (`../laravel_app/` vs `../`)
- **Always use `bash agent/deploy.sh`** — excludes index.php/.htaccess, includes pre/post health checks
- **Watchdog:** `agent/watchdog.sh` runs daily 5 AM via cron — checks 5 URLs, SSL expiry, response times
- **After deploy:** Always run `php artisan migrate --force` + clear caches on server

## Agent Ecosystem

This project is built by a swarm of AI agents. Roles definidos por el CEO — definitivos, no se discuten:

| Agente | Rol principal | Responsabilidades |
|---|---|---|
| **Antigravity** | Frontend / UI Lead | Diseño de interfaces, UX, propuestas visuales, arquitectura general. Tiene la palabra final en Frontend. |
| **Claude Code** | Full-Stack Engineer + Auditor | Implementación directa de código (Swift, Python, PHP, JS, CSS), auditoría, fixes de precisión. **Cuando Antigravity se queda sin tokens, Claude Code asume también el rol de Frontend.** |
| **Gemini CLI** | Infrastructure + Agent Builder | Backups, configuración de Ollama, creación y configuración de agentes Python, investigación de soporte para Dipper/Perry/William. No es el responsable principal de código de producción. |

### Regla de cobertura
- **Antigravity sin tokens** → Claude Code cubre Frontend + su propio rol hasta que Antigravity regrese.
- **Gemini CLI** apoya la investigación y alimenta a los agentes de inteligencia (Dipper, Perry, William) con datos y configuración.
- **El CEO** es quien asigna tareas — cualquier agente ejecuta lo que el CEO pida en sesión directa.

## Project Documents

| File | Purpose |
|------|---------|
| `AGENT_COMMUNICATION.md` | Active inter-agent mailbox (read before making changes) |
| `ENGINEERING_LOG.md` | Technical audit log: bugs, fixes, diffs, agent attribution — maintained by Claude Code |
| `legacy_backup/LESSONS_LEARNED.md` | Strategic lessons: design patterns, process decisions, multi-agent protocols |
| `agent/perry.py` | Reconnaissance agent — scrapes sources, discovers communities |
| `agent/dipper_scout.py` | Intelligence agent — extracts tools/questions from scraped data |
| `agent/william.py` | Blog writer — generates drafts with quality validation |
| `agent/deploy.sh` | Safe deploy script — NEVER overwrites index.php/htaccess |
| `agent/watchdog.sh` | Daily health check + metrics (cron 5 AM) |
| `agent/benchmark_models.py` | Ollama model comparison tool |
| `command-center/server.js` | Node.js dashboard (port 4000) + William feedback API |
| `command-center/public/demo_william.html` | Blog draft review + editorial feedback system |
| `app/Http/Middleware/CheckApproved.php` | Blocks unapproved users from protected routes |

## Critical Rules (learned from bugs — see ENGINEERING_LOG.md)

- **ENUM values in Filament forms must match the migration exactly.** SQLite accepts any string; MySQL enforces ENUM constraints. Always cross-check `->options([])` values against the migration definition.
- **Never use `/node_modules` in `.gitignore` for nested packages.** The leading slash restricts the pattern to the root only. Use `module-name/node_modules/` for subdirectory packages.
- **Every new `$fillable` field in a Filament form must also be added to the model's `$fillable` array.** Eloquent silently drops non-fillable fields on mass-assignment with no error.
- **Filament's `->authorize()` is required to restrict `/admin` by role.** The panel's own login does not filter by user role automatically.
- **Production `index.php` paths differ from development.** Production uses `../laravel_app/vendor/autoload.php`. Development uses `../vendor/autoload.php`. NEVER sync this file during deploy.
- **William drafts must pass validation before publishing.** Check for banned words (Reddit, infrastructure terms), fake statistics, and missing explanations. Editorial review at `localhost:4000/demo_william.html`.
- **Ollama model base is `gemma3:4b`.** Tested vs `qwen3:4b` — qwen3 failed due to incompatible thinking mode. Benchmark script: `agent/benchmark_models.py`.
