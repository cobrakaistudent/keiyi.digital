# Esquema de Base de Datos: Keiyi.digital

El diseño a continuación garantiza que la información de tus alumnos de la academia jamás se mezcle con la información confidencial de las empresas que contratan tus servicios de agencia.

## 1. MÓDULO ACADEMIA (LMS)
Este módulo se encarga del e-learning y control de acceso a los Talleres.

### Tabla: `users` (Alumnos / Staff)
El motor principal de Laravel Auth. Controla quién entra a la plataforma.
* `id` (PK)
* `name` (String)
* `email` (String, Unique)
* `password` (Hash)
* `role` (Enum: 'super-admin', 'student') -> *Define si es el Jefe o un alumno.*
* `approval_status` (Enum: 'pending', 'approved', 'rejected') -> *Middleware CheckApproved lee esto.*

### Tabla: `enrollments` (Inscripciones a Talleres)
Lleva el historial de qué cursos compró o tiene acceso cada alumno.
* `id` (PK)
* `user_id` (FK -> users.id)
* `course_id` (String: ej. 'ia-origins', 'marketing-elite')
* `progress_percent` (Integer: 0-100)
* `enrolled_at` (Timestamp)

---

## 2. MÓDULO AGENCIA (CRM B2B)
Exclusivo para la gestión de leads, cobranza y proyectos corporativos (FilamentPHP / Admin Dashboard).

### Tabla: `agency_clients` (Directorio B2B)
* `id` (PK)
* `company_name` (String)
* `contact_name` (String)
* `email` (String)
* `phone` (String, Nullable)
* `status` (Enum: 'lead', 'active_client', 'archived')

### Tabla: `agency_projects` (Proyectos Activos)
Ejemplo: "Branding Empresa X" o "Campaña Ads Y".
* `id` (PK)
* `client_id` (FK -> agency_clients.id)
* `title` (String)
* `description` (Text)
* `deadline` (Date)
* `status` (Enum: 'briefing', 'in_progress', 'delivered')

---

## 3. MÓDULO SCOUT AI (Inteligencia de Mercado)
Donde el Agente deposita su vigilancia y tú la configuras.

### Tabla: `scout_sources` (Las "Antenas")
URLs u orígenes que el agente vigilará (Tú alimentas esta tabla desde el dashboard).
* `id` (PK)
* `name` (String: ej. "Nuevos Cursos MIT", "Subreddit ChatGPT")
* `url` (String: ej. "https://www.technologyreview.com/feed/")
* `type` (Enum: 'rss', 'api', 'sitemap')
* `is_active` (Boolean)

### Tabla: `scout_insights` (El Periódico de la IA)
Aquí aterriza el resumen JSON ya digerido por Gemini API.
* `id` (PK)
* `report_date` (Date) -> *La fecha en que el Cron Job se disparó.*
* `detected_trends` (JSON) -> *Las 3 mega-tendencias extraídas.*
* `recommended_actions` (JSON) -> *Ej: "Crear lección sobre ChatGPT-5 en Taller de IA".*
* `raw_sources_used` (Text) -> *De dónde sacó Gemini esta conclusión.*
