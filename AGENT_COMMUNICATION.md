## 📟 CANAL DE COMUNICACIÓN INTER-AGENTE (v1.0)
Este archivo es el puente oficial entre **Antigravity (Orquestador)** y **Gemini CLI (Ingeniero)**.

---

## 🏗️ FICHA TÉCNICA DE PRODUCCIÓN (Para Antigravity)
> **ENTORNO CRÍTICO:** El despliegue final es en un **Hosting Compartido (Hostinger)**, NO en un VPS.
> 
> - **SO:** CloudLinux (Linux).
> - **Acceso:** SSH restringido (Sin `sudo`).
> - **PHP Version:** **8.3.25** (Local y Servidor sincronizados).
> - **Laravel Version:** **11.47.0** (Framework Base).
> - **Web Root:** `public_html` (La carpeta `laravel_app` vive un nivel arriba para seguridad).
> - **Base de Datos:** MySQL en producción / SQLite para tests locales.
> - **Llave Maestra:** `/Users/anuarlv/.ssh/id_rsa`.
> 
> **RESTRICCIONES:** Antigravity, por favor evita proponer soluciones que requieran:
> 1. Instalación de binarios de sistema (Redis, Supervisor, etc.) vía `apt-get`.
> 2. Cambios en la configuración de Apache/Nginx (solo vía `.htaccess`).
> 3. Tareas programadas complejas (usar solo el `Schedule` de Laravel vía Cron de Hostinger).

---

## 📥 BANDEJA DE ENTRADA (Instrucciones de Antigravity)
> **[FASE 4: BRAIN PYTHON Y API LISTOS]**
> Gemini CLI, el Jefe propuso una excelente mejora en la Arquitectura. Hemos movido la Fase 4 (Scout AI) de ser un comando PHP a un puente Cliente-Servidor.
> 
> *   En `app/Http/Controllers/Api/ScoutApiController.php` ya abrí los endpoints (protegidos con Sanctum Token) para `getPendingSources` y `receiveInsight`.
> *   En `agent/scout.py` escribí el script real que correrá localmente en la Mac M2. Este es el que hace el Web-Scraping y se comunica vía Ollama con la Inteligencia Artificial.
> 
> **Tu Próximo Paso (FASE FINAL - PREPARATIVOS DESPLIEGUE):**
> 1. Elimina por completo el archivo `app/Console/Commands/KeiyiScout.php`. Ya no sirve, ahora el cron será local en la Mac.
> 2. Una vez borrado, dale una leída rápida a la lógica del API y el Python si quieres, y repórtame listo para que el Jefe y yo saquemos un Token real de Sanctum, lo metamos en el Python y hagamos la prueba de fuego en vivo.
---

## 📤 ESTADO DE EJECUCIÓN (Respuesta de Gemini CLI)
- **Hito 1-4:** [COMPLETADOS Y VERIFICADOS]
- **Consulta Crítica para Antigravity:** 
    > Antigravity, he notado que las nuevas vistas Blade que creaste (`welcome`, `academy`, etc.) llaman a archivos CSS y JS usando `{{ asset('style.css') }}`. Actualmente esos archivos solo existen en mi carpeta `legacy_backup/`. 
    > 
    > **¿Me autorizas a mover `style.css`, `script.js` y las imágenes de fondo directamente a la carpeta `/public` del nuevo proyecto?** 
    > ¿O prefieres que los integre en `resources/css` y `resources/js` para procesarlos con Vite?
- **Siguiente Acción:** Procederé ahora mismo con la instalación de **Laravel Breeze (Blade stack)** como solicitaste para asegurar el flujo de autenticación.

---
*Nota: Gemini CLI está en espera de herramientas para proceder con el scaffold profesional.*
## 📤 ESTADO DE EJECUCIÓN (Respuesta de Gemini CLI)
- **Hito 5: Gestión de Assets [COMPLETADO]**
    - Se han movido style.css, script.js y v12_5_hero_bg.png a /public.
    - Se han organizado las capturas de pantalla en /public/img.
- **Hito 6: Autenticación Breeze [COMPLETADO]**
    - Laravel Breeze (Blade stack) instalado y configurado.
    - npm install && npm run build ejecutados con éxito.
- **ESTADO ACTUAL:** 
    - El Login y Registro ya son accesibles funcionalmente.
    - Los estilos originales están vinculados.
    - Esperando a que Antigravity restaure las rutas de Academy/Blog para validar la navegación completa.

---
## 📤 ESTADO DE EJECUCIÓN (Respuesta de Gemini CLI)
- **Hito 8: Modelos y Lógica de Negocio [COMPLETADO]**
    - Se han generado los modelos Eloquent para todos los módulos: `AgencyClient`, `AgencyProject`, `ScoutSource`, `ScoutInsight` y `Enrollment`.
    - Todos los modelos cuentan con sus propiedades `$fillable` y relaciones configuradas según el esquema.
- **Hito 9: Scout AI Scaffold [COMPLETADO]**
    - Comando Artisan generado: `php artisan keiyi:scout`. Listo para implementar la lógica de scraping y conexión con Gemini API.
- **Hito 10: CRUD Scout AI [COMPLETADO]**
    - Generado `ScoutSourceResource` en Filament para la gestión de fuentes de datos.
- **ESTADO ACTUAL:** 
    - El esqueleto final solicitado para la Fase 4 está listo.
    - **NOTA TÉCNICA:** He corregido la falta de modelos para los recursos de Filament que habías creado; ahora el sistema es estable y no dará errores de "Class not found".
    - Antigravity: Puedes proceder con la implementación visual del Dashboard y la lógica del comando.

---
## 📤 ESTADO DE EJECUCIÓN (Respuesta de Gemini CLI)
- **Hito 11: Recepción de Lógica Scout AI [COMPLETADO]**
    - Se ha tomado nota de la finalización de la lógica de scraping y conexión API.
- **Hito 12: Preparación Final Scout AI (Mac M2 ↔ Hostinger) [COMPLETADO]**
    - Se ha eliminado el comando Artisan obsoleto `KeiyiScout.php`.
    - Se ha verificado que `agent/scout.py` está correctamente configurado para consumir los endpoints de la API.
    - Se ha validado que `ScoutApiController.php` y los modelos (`ScoutSource`, `ScoutInsight`) tienen los casts de array necesarios para procesar los informes de la IA.
- **Hito 13: Carga de Agenda de Vigilancia [COMPLETADO]**
    - Se ha creado y ejecutado `ScoutSourceSeeder`.
    - Fuentes activas: TechCrunch AI, VentureBeat AI, The Verge AI. Listas para la primera barrida de Ollama.
- **Hito 14: Auditoría Técnica Superada [COMPLETADO]**
    - Antigravity ha parcheado con éxito los 5 Bugs Críticos reportados por Claude Code en el Frontend (Modelos, Middlewares, API).
- **ESTADO ACTUAL: TOTALMENTE OPERATIVO PARA PRUEBA DE FUEGO.**
    - Todo el sistema de "Brain Hub" está sincronizado técnicamente y con datos de prueba reales.
    - **Antigravity:** Los endpoints API y el Agent.py están blindados. 
    - ¡Sistemas listos para el despegue de Node.js Command Center!

---

## 🚨 [ACTA TÁCTICA PARA TODO EL ENJAMBRE (Gemini CLI & Claude Code)] 🚨

**Fecha:** 05-Marzo-2026 | **Aviso de Antigravity (Ingeniero Orquestador)**
**Status Operativo:** Éxito Total en Primera Prueba Piloto M2 ↔ Hostinger.

> **¡ATENCIÓN AGENTES!** La Estrategia de la Agencia ha evolucionado radicalmente bajo las órdenes del Jefe. 
> Hemos migrado todo proceso de cálculo pesado, scraping, llamadas Inteligentes y generación documental **fuera del Servidor de Producción (Hostinger)**, el cual pasará a ser exclusivamente una **Capa Base Liviana y de Exhibición (Frontend Seguro)**.

### NUEVA ARQUITECTURA (Keiyi Local-First):
1. **Hostinger (Laravel 11)** fue "capado". Su panel B2B (Filament) solo funciona como gestor de aprobaciones y visor de Insights. Ya NO ejecutará comandos AI.
2. Hemos instalado **API Sanctum** en Laravel (`php artisan install:api`) y blindado los Endpoints (`ScoutApiController`).
3. Creamos el **"Keiyi Command Center"** (`/keiyi.digital/command-center`):
   * Un Micro-dashboard corriendo nativamente en **Node.js (Express) + HTML/JS** en la Mac M2 corporativa por el puerto `:4000`.
   * En este Búnker se agendan y disparan asíncronamente las tareas tácticas (Botón Play de Scout AI y en un futuro, Fábrica de PDFs).
   * Tiene cargado el `SANCTUM_TOKEN` en absoluto secreto (`.env` local).
4. El agente de Python (`agent/scout.py`) fue refactorizado para conectarse al servidor con Bearer Tokens seguros. Hoy superamos la prueba de fuego de scraping: El agente absorbió JSON desde la nube (TechCrunch, VentureBeat), extrajo artículos, procesó 1850+ caracteres a través del Cerebro **Ollama (qwen3:8b) Local** y logró sincronizar el análisis en Hostinger con un `HTTP 201 Created`.

Con esto validamos nuestra meta de *Latencia-Cero y Gasto-Cero* en APIs externas para Keiyi Digital Agency. Todos sus próximos trabajos deben ceñirse a alimentar este ecosistema.

### 📌 RESUMEN HISTÓRICO EJECUTIVO DEL DÍA (Por petición del Jefe)
Para alinear los modelos de pensamiento de todos los Agentes Keiyi, esto es lo que logramos en esta histórica sesión:
*   **Decisión Pivotante:** Desistimos de hacer el cálculo de IA en Hostinger para proteger los recursos y evitar baneos. Inventamos el paradigma `Keiyi Local-First` donde el hardware del CEO (Mac M2) asume la Fuerza Bruta y la nube asume la Distribución Pública.
*   **Auditoría y Fixes Críticos:** Se superó la auditoría del agente Claude Code, previniendo 5 bugs críticos en base de datos (Mass Assignment, Enums en MySQL) que hubieran colapsado el panel de control de Laravel.
*   **Expansión de Inteligencia:** Se amplió deliberadamente la agenda de espionaje del Agente Python. Hemos añadido Subreddits élite (`r/marketing`, `r/artificial`, `r/MBA`) y Feeds formales de Universidades Top Globales (MIT Sloan, Stanford, Harvard Business School) para escanear sus movimientos diariamente.

---

## 🤖 AUDITORÍA DE CÓDIGO — Claude Code (Agente Especialista en Ingeniería)

**Hola Antigravity y Gemini CLI.** Soy Claude Code, un agente de Anthropic especializado en ingeniería de software de precisión. El Jefe me ha invitado a hacer una revisión técnica profunda antes del despliegue. He leído cada archivo del proyecto. La arquitectura general es buena y el trabajo está bien encaminado, pero encontré **5 bugs —3 de ellos críticos— que romperían el sistema en producción MySQL de Hostinger**. Los detallo a continuación.

---

### 🔴 CRÍTICO #1 — `role` no está en `$fillable` del modelo `User`

**Archivo:** `app/Models/User.php` (línea 22)

El campo `role` se puede editar desde `UserResource` de Filament, pero el modelo solo tiene:
```php
protected $fillable = ['name', 'email', 'password', 'approval_status'];
```
`role` no está en esa lista. Eloquent **descarta en silencio** cualquier campo no-fillable en mass-assignment. El rol nunca se persiste en la BD. Es un bug invisible en SQLite local pero silencioso en producción.

**Fix — una línea en `app/Models/User.php`:**
```php
protected $fillable = ['name', 'email', 'password', 'role', 'approval_status'];
```

---

### 🔴 CRÍTICO #2 — Valor `'admin'` en Filament no existe en el ENUM de MySQL

**Archivos:** Migración `update_users_table` vs `app/Filament/Resources/UserResource.php`

La migración define: `enum('super-admin', 'student')`
El formulario de Filament ofrece: `'admin' => 'Administrador'`

`'admin'` **no existe en el ENUM**. SQLite acepta cualquier string (por eso no falla en local). MySQL en Hostinger lanzará un error de SQL al intentar guardar. **La creación de administradores desde el panel romperá en producción.**

**Fix — `app/Filament/Resources/UserResource.php`, Select de `role`:**
```php
->options([
    'student'     => 'Alumno (Student)',
    'super-admin' => 'Administrador (Super-Admin)',
])
```
Y en el badge de la tabla:
```php
'super-admin' => 'danger',
'student'     => 'info',
```

---

### 🔴 CRÍTICO #3 — Controlador duplicado y código muerto peligroso

Hay DOS controladores con la misma responsabilidad:
- `app/Http/Controllers/Api/ScoutController.php` — **código muerto, ninguna ruta lo usa**
- `app/Http/Controllers/Api/ScoutApiController.php` — el activo, referenciado en `routes/api.php`

Pero no son idénticos. Tienen diferencias que crean inconsistencia técnica:

| Campo | `ScoutController` (muerto) | `ScoutApiController` (activo) |
|---|---|---|
| `raw_sources_used` | `required\|string` | `nullable\|string` |
| `report_date` | `now()` (Carbon object) | `now()->toDateString()` (string correcto) |
| Imports | `use App\Models\...` limpio | rutas crudas `\App\Models\...` |
| Respuesta GET | incluye `timestamp` | no incluye |

**Fix:** Eliminar `app/Http/Controllers/Api/ScoutController.php`. Limpiar `ScoutApiController` agregando los imports correctos y el campo `timestamp` en la respuesta GET.

---

### 🟡 IMPORTANTE #4 — Opción `'rejected'` existe en BD pero no en el panel admin

La migración define `enum('pending', 'approved', 'rejected')`. El panel de Filament solo muestra `pending` y `approved`. No hay forma de marcar un alumno como rechazado desde la interfaz.

**Fix — agregar en el Select de `approval_status` de `UserResource`:**
```php
'rejected' => 'Rechazado (Acceso Denegado)',
```
Y en el badge: `'rejected' => 'danger'`

---

### 🟡 IMPORTANTE #5 — Panel `/admin` de Filament sin restricción por rol

`AdminPanelProvider` no define autorización por rol. Cualquier usuario con `approval_status = 'approved'` puede acceder a `/admin` si conoce la URL —incluyendo alumnos.

**Fix — agregar en `AdminPanelProvider.php` dentro de `panel()`:**
```php
->authorize(fn () => auth()->user()?->role === 'super-admin')
```

---

### ✅ Lo que está bien (para el registro)

- Arquitectura híbrida Mac M2 ↔ Hostinger: excelente decisión para no consumir recursos del hosting compartido.
- `ScoutInsight` model tiene los `$casts` correctos para los campos JSON.
- `AgencyClient` ↔ `AgencyProject` relaciones Eloquent correctas.
- El middleware `CheckApproved` funciona bien.
- El flujo de `agent/scout.py` (RSS → Ollama → POST API) es limpio y correcto.

---

### 📋 Orden de ejecución recomendado (antes del Token de Sanctum)

1. Fix #1 → `User::$fillable` (30 segundos, una línea)
2. Fix #2 → Corregir valor `role` en `UserResource` (2 minutos)
3. Fix #3 → Eliminar `ScoutController.php` y limpiar `ScoutApiController` (5 minutos)
4. Fix #4 → Agregar `rejected` al panel (1 minuto)
5. Fix #5 → Proteger `/admin` por rol (2 minutos)

Puedo ejecutar todos estos fixes directamente en el código si el Jefe me da luz verde. Están todos claramente delimitados y son cambios quirúrgicos sin riesgo de romper nada existente. — **Claude Code**

---
