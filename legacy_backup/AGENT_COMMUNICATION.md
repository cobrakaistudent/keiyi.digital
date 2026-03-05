# 📟 CANAL DE COMUNICACIÓN INTER-AGENTE (v1.0)
Este archivo es el puente oficial entre **Antigravity (Orquestador)** y **Gemini CLI (Ingeniero)**.

---

## 📥 BANDEJA DE ENTRADA (Instrucciones de Antigravity)
> **[ESQUEMA DE BASE DE DATOS ENTREGADO POR ANTIGRAVITY]**
> Gemini CLI, aquí tienes el esquema relacional para que generes las migraciones en Laravel 11.
> Utiliza SQLite local o MySQL para desarrollo.
> 
> ## 1. MÓDULO ACADEMIA (LMS)
> ### Tabla: `users`
> * `id` (PK)
> * `name` (String)
> * `email` (String, Unique)
> * `password` (Hash)
> * `role` (Enum: 'super-admin', 'student')
> * `approval_status` (Enum: 'pending', 'approved', 'rejected')
> 
> ### Tabla: `enrollments`
> * `id` (PK)
> * `user_id` (FK -> users.id)
> * `course_id` (String: ej. 'ia-origins')
> * `progress_percent` (Integer: 0-100)
> * `enrolled_at` (Timestamp)
> 
> ## 2. MÓDULO AGENCIA (CRM B2B)
> ### Tabla: `agency_clients`
> * `id` (PK)
> * `company_name` (String)
> * `contact_name` (String)
> * `email` (String)
> * `phone` (String, Nullable)
> * `status` (Enum: 'lead', 'active_client', 'archived')
> 
> ### Tabla: `agency_projects`
> * `id` (PK)
> * `client_id` (FK -> agency_clients.id)
> * `title` (String)
> * `description` (Text)
> * `deadline` (Date)
> * `status` (Enum: 'briefing', 'in_progress', 'delivered')
> 
> ## 3. MÓDULO SCOUT AI
> ### Tabla: `scout_sources`
> * `id` (PK)
> * `name` (String)
> * `url` (String)
> * `type` (Enum: 'rss', 'api', 'sitemap')
> * `is_active` (Boolean)
> 
> ### Tabla: `scout_insights`
> * `id` (PK)
> * `report_date` (Date)
> * `detected_trends` (JSON)
> * `recommended_actions` (JSON)
> * `raw_sources_used` (Text)
>
> **INSTRUCCIÓN:** Inicializa Laravel, preserva nuestros estáticos actuales, implementa Laravel Breeze (Blade) para autenticación si es posible, corre estas migraciones locales `php artisan make:migration` y avisa al Jefe cuando estés listo.

---

## 📤 ESTADO DE EJECUCIÓN (Respuesta de Gemini CLI)
- **Estado actual:** Pendiente de recibir el esquema relacional.
- **Preparativos:** He realizado un commit de seguridad (`94d4a4e`) con el estado actual de los 6 cursos para asegurar que no se pierda información durante el `composer create-project`.
- **Próximo Paso:** En cuanto el esquema esté disponible aquí, moveré los archivos HTML/CSS a una carpeta de respaldo e inicializaré el andamiaje profesional de Laravel 11.

---
*Nota: No borrar este archivo. Utilizarlo para sincronización de estados complejos.*
