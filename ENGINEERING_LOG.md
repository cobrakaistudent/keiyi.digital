# ENGINEERING_LOG.md — Keiyi Digital

Bitácora técnica de auditorías, bugs y cambios aplicados al proyecto.
Mantenida por **Claude Code** (Agente de Ingeniería de Precisión).

> **Uso:** Este documento es el registro de trazabilidad técnica. Para patrones de diseño y lecciones estratégicas, ver `legacy_backup/LESSONS_LEARNED.md`. Para guía de arquitectura general, ver `CLAUDE.md`.

**Agentes del enjambre:**
- **Antigravity** — Orquestador. Define arquitectura, escribe controladores y configuraciones.
- **Gemini CLI** — Ingeniero de implementación. Ejecuta scaffolding, modelos, recursos Filament.
- **Claude Code** — Auditor de ingeniería. Revisa código, aplica fixes de precisión, mantiene este log.

---

## FORMATO DE ENTRADA

```
### BUG-XXX — Título breve
- Fecha: YYYY-MM-DD
- Severidad: Crítico | Importante | Menor
- Origen probable: Antigravity | Gemini CLI | Ambos
- Archivo(s): ruta/al/archivo.php
- Detectado por: [agente]
- Resuelto por: [agente] en [fecha]
- Tiempo estimado del fix: X min
```

---

## AUDITORÍA #1 — 2026-03-05

**Auditor:** Claude Code
**Disparador:** Primera revisión completa antes de la prueba de fuego con Sanctum Token (Hito 13).
**Resultado:** 5 bugs encontrados. Los bugs 1-4 fueron parchados por Antigravity (Hito 14). El bug 5 fue aplicado por Claude Code directamente.

---

### BUG-001 — `role` nunca se persistía en la base de datos

- **Fecha:** 2026-03-05
- **Severidad:** Critico
- **Origen probable:** Antigravity (diseñó `UserResource`) + Gemini CLI (generó modelo `User`)
- **Archivo:** `app/Models/User.php`
- **Detectado por:** Claude Code
- **Resuelto por:** Antigravity (Hito 14)
- **Tiempo del fix:** ~1 min

**Problema:** El campo `role` estaba presente en el formulario de Filament pero ausente de `$fillable` en el modelo. Eloquent descarta silenciosamente campos no-fillable en mass-assignment — sin lanzar excepción ni warning. El bug era invisible en SQLite (permisivo) y habría explotado en MySQL de Hostinger.

```php
// ANTES — app/Models/User.php
protected $fillable = ['name', 'email', 'password', 'approval_status'];

// DESPUÉS
protected $fillable = ['name', 'email', 'password', 'role', 'approval_status'];
```

---

### BUG-002 — Valor `'admin'` inexistente en ENUM de MySQL

- **Fecha:** 2026-03-05
- **Severidad:** Critico
- **Origen probable:** Antigravity (migración con `super-admin`) + Gemini CLI (`UserResource` con `'admin'`)
- **Archivo:** `app/Filament/Resources/UserResource.php`
- **Detectado por:** Claude Code
- **Resuelto por:** Antigravity (Hito 14)
- **Tiempo del fix:** ~2 min

**Problema:** La migración define `enum('super-admin', 'student')`. El Select de Filament ofrecía `'admin'` como opción. MySQL en Hostinger lanza error de integridad al intentar insertar un valor fuera del ENUM. SQLite acepta cualquier string, por eso el bug no aparecía en local.

```php
// ANTES — UserResource.php
->options(['student' => 'Alumno', 'admin' => 'Administrador'])

// DESPUÉS
->options(['student' => 'Alumno (Student)', 'super-admin' => 'Administrador (Super-Admin)'])
```

**Nota técnica:** El valor en el Select debe ser el string exacto del ENUM definido en la migración. No es un label, es el valor que va a la BD.

---

### BUG-003 — Controlador duplicado con lógica divergente

- **Fecha:** 2026-03-05
- **Severidad:** Critico
- **Origen probable:** Antigravity (creó ambos en diferentes fases sin eliminar el primero)
- **Archivos:** `app/Http/Controllers/Api/ScoutController.php` (eliminado), `app/Http/Controllers/Api/ScoutApiController.php` (conservado)
- **Detectado por:** Claude Code
- **Resuelto por:** Antigravity (Hito 14)
- **Tiempo del fix:** ~1 min

**Problema:** Dos controladores para el mismo endpoint, solo uno referenciado en rutas. El código muerto tenía diferencias técnicas sutiles vs. el activo:

| Diferencia | `ScoutController` (muerto) | `ScoutApiController` (activo) |
|---|---|---|
| `raw_sources_used` | `required\|string` | `nullable\|string` |
| `report_date` | `now()` (Carbon object) | `now()->toDateString()` (string) |
| Imports | `use App\Models\...` | rutas crudas `\App\Models\...` |

**Resolución:** Eliminar `ScoutController.php`. La ruta activa en `routes/api.php` ya apuntaba a `ScoutApiController`, por lo que no hubo cambio de comportamiento.

---

### BUG-004 — Estado `'rejected'` sin representación en el panel admin

- **Fecha:** 2026-03-05
- **Severidad:** Importante
- **Origen probable:** Gemini CLI (implementó `UserResource` sin cubrir todos los valores del ENUM)
- **Archivo:** `app/Filament/Resources/UserResource.php`
- **Detectado por:** Claude Code
- **Resuelto por:** Antigravity (Hito 14)
- **Tiempo del fix:** ~1 min

**Problema:** La migración define `enum('pending', 'approved', 'rejected')`. El panel de Filament solo exponía `pending` y `approved`. No era posible rechazar a un alumno desde la interfaz — operación necesaria en el flujo de aprobación de la Academia.

```php
// AGREGADO al Select de approval_status
'rejected' => 'Rechazado (Acceso Denegado)',

// AGREGADO al badge de la tabla
'rejected' => 'danger',
```

---

### BUG-005 — Panel `/admin` de Filament accesible por cualquier usuario aprobado

- **Fecha:** 2026-03-05
- **Severidad:** Importante
- **Origen probable:** Antigravity (configuró `AdminPanelProvider` sin restricción de rol)
- **Archivo:** `app/Providers/Filament/AdminPanelProvider.php`
- **Detectado por:** Claude Code
- **Resuelto por:** Claude Code (2026-03-05)
- **Tiempo del fix:** ~2 min

**Problema:** `AdminPanelProvider` no definía `->authorize()`. Filament tiene su propio formulario de login pero no restringe por rol. Un alumno con `approval_status = 'approved'` podía acceder al CRM completo (clientes, proyectos, usuarios) navegando a `/admin`.

```php
// AGREGADO — AdminPanelProvider.php, al final de la cadena panel()
->authorize(fn () => auth()->user()?->role === 'super-admin');
```

**Justificación técnica:** `->authorize()` es el hook oficial de Filament 3 para control de acceso al panel. El operador nullsafe `?->` evita fatal error si no hay sesión activa. Se compara contra `'super-admin'` — valor exacto del ENUM en la migración.

---

## AUDITORÍA #2 — 2026-03-05

**Auditor:** Claude Code
**Disparador:** Revisión del nuevo Command Center (Node.js/Express) tras confirmar la primera prueba de fuego exitosa (HTTP 201 desde Mac M2 a Hostinger).
**Resultado:** 2 issues encontrados, ambos resueltos por Claude Code directamente.

---

### BUG-006 — `command-center/node_modules/` no excluido de git

- **Fecha:** 2026-03-05
- **Severidad:** Critico
- **Origen probable:** Antigravity (creó el Command Center sin actualizar `.gitignore`)
- **Archivo:** `.gitignore`
- **Detectado por:** Claude Code
- **Resuelto por:** Claude Code (2026-03-05)
- **Tiempo del fix:** ~1 min

**Problema:** El `.gitignore` raíz tenía `/node_modules` con slash inicial. En git, un path con `/` inicial solo aplica al directorio raíz. El `command-center/node_modules/` (70+ paquetes, miles de archivos) quedaba expuesto y habría entrado al historial de git en el próximo `git push`.

```gitignore
# AGREGADO al final de .gitignore
# Command Center (Node.js local)
command-center/node_modules/
command-center/.env
```

**Nota adicional:** El `.env` del Command Center contiene el Sanctum Token de producción. Aunque el patrón `.env` sin slash debería cubrirlo, se agregó explícitamente por ser una credencial de producción.

---

### BUG-007 — CORS abierto en Command Center

- **Fecha:** 2026-03-05
- **Severidad:** Importante
- **Origen probable:** Antigravity (inicializó Express con `cors()` sin configuración)
- **Archivo:** `command-center/server.js`
- **Detectado por:** Claude Code
- **Resuelto por:** Claude Code (2026-03-05)
- **Tiempo del fix:** ~1 min

**Problema:** `app.use(cors())` sin parámetros acepta peticiones de cualquier origen. En una red compartida (oficina, coworking), cualquier dispositivo en la red local podría disparar el Scout AI — que consume recursos de Ollama y realiza escrituras en producción de Hostinger.

```javascript
// ANTES
app.use(cors());

// DESPUÉS
app.use(cors({ origin: ['http://localhost:4000', 'http://127.0.0.1:4000'] }));
```

**Justificación técnica:** El Command Center es un servicio estrictamente local. Se incluyen ambas variantes de loopback (`localhost` y `127.0.0.1`) porque algunos browsers resuelven el origen diferente dependiendo del sistema operativo.

---

## TABLA RESUMEN ACUMULADA

| ID | Severidad | Origen | Archivo | Resuelto por | Fecha |
|---|---|---|---|---|---|
| BUG-001 | Critico | Antigravity + Gemini CLI | `User.php` | Antigravity | 2026-03-05 |
| BUG-002 | Critico | Antigravity + Gemini CLI | `UserResource.php` | Antigravity | 2026-03-05 |
| BUG-003 | Critico | Antigravity | `ScoutController.php` | Antigravity | 2026-03-05 |
| BUG-004 | Importante | Gemini CLI | `UserResource.php` | Antigravity | 2026-03-05 |
| BUG-005 | Importante | Antigravity | `AdminPanelProvider.php` | Claude Code | 2026-03-05 |
| BUG-006 | Critico | Antigravity | `.gitignore` | Claude Code | 2026-03-05 |
| BUG-007 | Importante | `server.js` | `command-center/server.js` | Claude Code | 2026-03-05 |

**Total bugs:** 7 | **Críticos:** 4 | **Importantes:** 3 | **Pendientes:** 0

---

## PATRONES DE ERROR POR AGENTE

### Antigravity
- Introduce bugs de **configuración entre entornos**: valores que SQLite tolera pero MySQL rechaza (ENUMs, tipos).
- Olvida actualizar **archivos de infraestructura** (`.gitignore`, permisos) al agregar nuevos módulos.
- Tiende a crear **código duplicado** entre fases sin limpiar el anterior.

### Gemini CLI
- Implementa estructuras **incompletas**: cubre el happy path pero omite casos edge definidos en la migración (ej. estados del ENUM no reflejados en la UI).
- Buena calidad en relaciones Eloquent y casts de modelos.

---

---

## AUDITORÍA #3 — 2026-03-05

**Auditor:** Claude Code
**Disparador:** Misión de desarrollo del Deep Web Crawler (Fase 5 Scout AI). Revisión previa al desarrollo.
**Resultado:** 1 bug crítico detectado. Corregido antes de escribir el código Python.

---

### BUG-008 — `'web'` no existe en el ENUM de MySQL de `scout_sources.type`

- **Fecha:** 2026-03-05
- **Severidad:** Critico
- **Origen probable:** Antigravity (actualizó modelo y Filament pero olvidó actualizar el ENUM en la migración)
- **Archivos:** `database/migrations/2026_03_05_232056_add_relevance_score_to_scout_sources_table.php` (incompleta)
- **Detectado por:** Claude Code (revisión previa al desarrollo)
- **Resuelto por:** Claude Code — nueva migración `2026_03_05_235900_add_web_type_to_scout_sources_enum.php`
- **Tiempo del fix:** ~3 min

**Problema:** El mismo patrón que BUG-002. La migración `add_relevance_score` agregó columnas nuevas pero no modificó el ENUM de la columna `type`. El estado en la BD seguía siendo `ENUM('rss', 'api', 'sitemap')`. El `ScoutSourceResource` en Filament ya ofrecía `'web'` como opción. En SQLite local funciona (no hay ENUMs, acepta cualquier string). En MySQL de Hostinger, insertar `type = 'web'` causa error de integridad de datos.

**Decisión técnica — Por qué `DB::statement()` y no `->change()`:**
- `doctrine/dbal` no está instalado (verificado en `composer.json`). Sin él, `->change()` sobre columnas ENUM no es confiable en MySQL.
- `DB::statement()` con SQL directo funciona en cualquier versión de Laravel, sin dependencias adicionales, y es la forma más transparente de modificar ENUMs en MySQL.
- Se agregó un guard `DB::getDriverName() === 'mysql'` para que la migración no falle en SQLite local (que no entiende `MODIFY COLUMN`).

```php
// Nueva migración: 2026_03_05_235900_add_web_type_to_scout_sources_enum.php
if (DB::getDriverName() === 'mysql') {
    DB::statement("ALTER TABLE scout_sources MODIFY COLUMN type ENUM('rss', 'api', 'sitemap', 'web') NOT NULL");
}
```

---

### FEATURE-001 — Deep Web Crawler integrado en `agent/scout.py`

- **Fecha:** 2026-03-05
- **Desarrollado por:** Claude Code
- **Archivos modificados:** `agent/scout.py`, `agent/requirements.txt` (nuevo)
- **Restricciones aplicadas:** `scout.py` corre en Mac M2 local — las restricciones de Hostinger no aplican aquí. `beautifulsoup4` instalable vía `pip` sin `sudo`.

**Cambios aplicados:**

**1. Nueva función `extract_web_content(url, name)`**
- Headers de navegador real (`User-Agent` Chrome/Mac) para evitar bloqueos básicos
- Timeout de 20s (mayor que RSS por páginas más pesadas)
- BeautifulSoup con `html.parser` (parser built-in de Python, sin dependencias extra)
- Elimina ruido: `script`, `style`, `nav`, `footer`, `aside`, `header`, `iframe`, `noscript`, `form`, `button`
- Busca contenedor principal en orden de prioridad: `main` → `article` → elemento con id/class de curriculum → `body`
- Extrae texto de `h1`-`h4`, `p`, `li` con más de 25 caracteres (filtra labels y botones)
- Limita salida a 4000 caracteres para no saturar el contexto de Ollama

**2. Guard de importación de BeautifulSoup**
- Si `bs4` no está instalada, el script no falla — muestra advertencia y omite fuentes `'web'`
- Instrucción clara: `pip install -r agent/requirements.txt`

**3. Dispatch por tipo en `main()`**
```python
# ANTES — solo manejaba rss/sitemap
if type_s in ['rss', 'sitemap']:
    text = extract_rss_headlines(url)

# DESPUÉS — despacha al extractor correcto
if type_s in ['rss', 'sitemap']:
    text = extract_rss_headlines(url)
    super_context += f"\n--- Titulares de {name} ---\n{text}\n"
elif type_s == 'web':
    text = extract_web_content(url, name)
    super_context += f"\n--- Contenido Académico de {name} ---\n{text}\n"
else:
    print(f"Tipo '{type_s}' no soportado aún. Omitiendo.")
```

**4. Prompt de Ollama actualizado**
- Antes decía "noticias de las últimas 24 horas" — incorrecto para currículas
- Ahora: "Puede incluir titulares de noticias recientes Y/O descripciones de currículas académicas"
- El etiquetado de secciones en el contexto (`--- Titulares ---` vs `--- Contenido Académico ---`) le da a Ollama el contexto necesario para distinguir fuentes

**5. `agent/requirements.txt` creado**
```
requests>=2.31.0
beautifulsoup4>=4.12.0
```

---

### BUG-009 — Error Crítico en Arranque (Macro inexistente `authorize()` en PanelProvider)

- **Fecha:** 2026-03-05
- **Severidad:** Crítico (Caída total de Laravel - Hub de Aprobación Inaccesible)
- **Origen probable:** Claude Code
- **Archivo:** `app/Providers/Filament/AdminPanelProvider.php`
- **Detectado por:** Antigravity (tras caída de DB migration)
- **Resuelto por:** Antigravity
- **Tiempo del fix:** ~10 min

**Problema:** En la Auditoría #1 y #2, Claude Code inyectó y re-introdujo la cláusula `->authorize(fn () => auth()->user()?->role === 'super-admin')` en el `AdminPanelProvider.php`. Este método existía o se emulaba en otra versión de Filament, pero en **Filament v3.x**, usarlo en el Provider dispara una `BadMethodCallException` letal en el arranque de la app, que tira incluso comandos de terminal como `php artisan migrate` y la carga visual.

**Solución:** Se erradicó por completo el encadenamiento `->authorize()` del provider. El middleware de Filament y la estructura base actual ya se encarga del workflow de UI; intentar forzar policies en el provider sin registrar las clases bloquea el Service Container completo.

```php
// ANTES (Causante del Crash 500 / BadMethodCallException)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authorize(fn () => auth()->user()?->role === 'super-admin');

// DESPUÉS (Limpio y estable)
            ->authMiddleware([
                Authenticate::class,
            ]);
```

---

### CORRECCIÓN DE BUG-009 — Implementación correcta de restricción de panel Filament v3

- **Fecha:** 2026-03-05
- **Resuelto por:** Claude Code (corrección propia)
- **Archivos:** `app/Models/User.php`

**Post-mortem del error:** Apliqué `->authorize()` basándome en documentación genérica de Laravel sin verificar si Filament v3 expone ese método en su clase `Panel`. No lo hace — `Panel` no extiende ninguna clase base de Laravel que tenga macros activos por defecto. El error fue no leer el código fuente de Filament antes de proponer el fix.

**Lección registrada:** Siempre verificar en `vendor/` que el método exista en la clase concreta antes de proponer su uso. `->authorize()` existe en el Router de Laravel, no en el Panel de Filament.

**Fix correcto — Interfaz `FilamentUser` en el modelo `User`:**

El mecanismo oficial de Filament v3 para restringir acceso al panel es implementar la interfaz `FilamentUser` en el modelo de usuario. Filament llama a `canAccessPanel()` automáticamente en cada request al panel. Si retorna `false`, Filament rechaza el acceso con 403 sin necesidad de tocar el Provider.

```php
// app/Models/User.php — CAMBIOS APLICADOS

// Imports agregados:
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// Clase actualizada:
class User extends Authenticatable implements FilamentUser

// Método agregado:
public function canAccessPanel(Panel $panel): bool
{
    return $this->role === 'super-admin';
}
```

**Ventajas de este enfoque vs `->authorize()` en el Provider:**
- Es el método oficial documentado en Filament v3 — verificado en `vendor/filament/filament/src/Models/Contracts/FilamentUser.php`
- No toca `AdminPanelProvider` — cero riesgo de `BadMethodCallException`
- Testeable unitariamente: `$user->canAccessPanel($panel)`
- Permite lógica más compleja en el futuro (multi-panel, tenants, etc.)

---

### FEATURE-002 — Fábrica de Reportes Ejecutivos (Command Center)

- **Fecha:** 2026-03-05
- **Desarrollado por:** Claude Code
- **Archivos modificados:**
  - `app/Http/Controllers/Api/ScoutApiController.php` — nuevo método `getInsights()`
  - `routes/api.php` — nueva ruta `GET /api/scout/insights`
  - `command-center/server.js` — endpoint `/api/generate-report` + función `buildReportHTML()`
  - `command-center/public/index.html` — panel activado + lógica JS del botón

**Decisión técnica — por qué HTML/Print y no Puppeteer ni PDFKit:**

| Opción | Pros | Contras |
|---|---|---|
| Puppeteer | PDF real programático | ~300MB Chromium, lento, overkill |
| PDFKit | PDF sin browser | Diseño muy limitado, sin CSS |
| **HTML + @media print** | CSS completo, cero deps, nativo Mac | Usuario hace un clic extra |

En Mac M2 la exportación "Imprimir → Guardar como PDF" es nativa del OS y produce PDFs de alta calidad respetando todo el CSS. Es la solución más ligera y visualmente superior para este contexto Local-First.

**Flujo completo:**
1. Usuario hace clic en "Generar Reporte" en el dashboard (port 4000)
2. `GET /api/generate-report` en Node.js llama a `GET /api/scout/insights` en Hostinger (Sanctum)
3. Node.js toma el insight más reciente y llama a `buildReportHTML(insight)`
4. El HTML se envía al browser del usuario
5. Se abre en nueva pestaña con el reporte visual completo
6. Botón "Guardar como PDF" dispara `window.print()` → "Guardar como PDF" en Mac

**Diseño del reporte (branding Keiyi "Pop"):**
- Space Grotesk font — identidad de marca
- Header negro con acento lima `#a3e635`
- Tarjetas de tendencias neo-brutalist: bordes 3px, box-shadow offset
- Bloque amarillo `#facc15` para acciones con checks verdes
- Footer con fuentes y timestamp de generación
- `@media print`: fondo blanco, oculta botón, elimina sombras para PDF limpio

---

---

### ARCH-001 — Mandato "API-Less Design" (#1427)

- **Fecha:** 2026-03-05
- **Decisión de:** Antigravity (por mandato del Jefe)
- **Archivos afectados:** `routes/api.php`, `agent/scout.py`, `command-center/server.js`

**Cambio arquitectónico:** Todos los endpoints Sanctum/HTTP fueron comentados. La comunicación Mac↔Hostinger opera exclusivamente vía SSH/SCP + `subprocess`/`child_process.exec` ejecutando `php -r` en remoto.

**Evaluación de seguridad (Claude Code):** Decisión correcta para este caso de uso.
- SSH: autenticación por clave RSA (fuera de banda), cifrado por diseño, sin endpoints HTTP escaneables
- REST API: superficie pública atacable, token en headers, requiere HTTPS correctamente configurado
- Riesgo residual residual: `shell=True` en subprocesos — mitigado porque los valores de la BD son insertados por los propios agentes/admin, no por usuarios finales

---

### BUG-010 — Botón "Fábrica de Reportes" sin ID — event listener nunca se adjuntaba

- **Fecha:** 2026-03-05
- **Severidad:** Importante (funcionalidad invisible para el usuario)
- **Origen:** Claude Code (entrega previa incompleta — el backend existía pero el HTML estaba deshabilitado)
- **Archivo:** `command-center/public/index.html` (líneas 109-112)
- **Resuelto por:** Claude Code

**Problema:** El botón tenía `opacity-50 cursor-not-allowed` y no tenía `id="btnGenerateReport"`. El JS en la línea 269 hacía `document.getElementById('btnGenerateReport').addEventListener(...)` — al no existir el elemento con ese ID, el script lanzaba `TypeError: Cannot read properties of null` silenciosamente y el botón nunca respondía. Tampoco existía `div#reportStatus` que el JS esperaba.

**Fix:**
```html
<!-- ANTES -->
<button class="... opacity-50 cursor-not-allowed">
    Generador de Informes (Fase 5.b)
</button>

<!-- DESPUÉS -->
<div id="reportStatus" class="terminal-box w-full mb-4 hidden whitespace-pre-wrap text-xs"></div>
<button id="btnGenerateReport" class="w-full funky-border bg-black text-white py-4 font-black text-lg hover:bg-gray-800 transition-colors">
    Generar Reporte Ejecutivo PDF
</button>
```

---

### BUG-011 (cosmético) — `import axios` muerto en `server.js` + `import os` duplicado en `scout.py`

- **Fecha:** 2026-03-05
- **Severidad:** Menor (no afecta funcionalidad)
- **Origen:** Antigravity (refactorización API-Less dejó restos de código anterior)
- **Archivos:** `command-center/server.js` (línea 6), `agent/scout.py` (líneas 3-4)
- **Resuelto por:** Claude Code

**Fixes:** Eliminado `const axios = require('axios')` de `server.js`. Eliminado `import os` duplicado de `scout.py`.

---

## TABLA RESUMEN ACUMULADA (actualizada)

| ID | Severidad | Origen | Archivo | Resuelto por | Fecha |
|---|---|---|---|---|---|
| BUG-001 | Critico | Antigravity + Gemini CLI | `User.php` | Antigravity | 2026-03-05 |
| BUG-002 | Critico | Antigravity + Gemini CLI | `UserResource.php` | Antigravity | 2026-03-05 |
| BUG-003 | Critico | Antigravity | `ScoutController.php` | Antigravity | 2026-03-05 |
| BUG-004 | Importante | Gemini CLI | `UserResource.php` | Antigravity | 2026-03-05 |
| BUG-005 | Importante | Antigravity | `AdminPanelProvider.php` | Claude Code | 2026-03-05 |
| BUG-006 | Critico | Antigravity | `.gitignore` | Claude Code | 2026-03-05 |
| BUG-007 | Importante | Antigravity | `server.js` | Claude Code | 2026-03-05 |
| BUG-008 | Critico | Antigravity | `scout_sources` ENUM | Claude Code | 2026-03-05 |
| BUG-009 | Critico | Claude Code | `AdminPanelProvider.php` | Antigravity + Claude Code | 2026-03-05 |
| BUG-010 | Importante | Claude Code | `index.html` | Claude Code | 2026-03-05 |
| BUG-011 | Menor | Antigravity | `server.js`, `scout.py` | Claude Code | 2026-03-05 |
| ARCH-001 | — | Jefe/Antigravity | Arquitectura global | N/A (mandato) | 2026-03-05 |
| FEATURE-001 | — | Claude Code | `agent/scout.py` | N/A | 2026-03-05 |
| FEATURE-002 | — | Claude Code | Command Center | N/A | 2026-03-05 |

**Total bugs:** 11 | **Críticos:** 5 | **Importantes:** 3 | **Menores:** 1 | **Pendientes:** 0

---

---

## AUDITORÍA #4 — 2026-03-05

**Auditor:** Claude Code
**Disparador:** Fixes de Command Center (botones rotos), adición de fuentes de marketing, sistema de versionado de prompts.
**Resultado:** 5 bugs encontrados y resueltos. 2 features entregadas.

---

### BUG-012 — `require('fs')` declarado en medio de las rutas (no en imports)

- **Fecha:** 2026-03-05
- **Severidad:** Menor (funcional pero frágil por convención)
- **Origen probable:** Antigravity (lo declaró en la línea ~124, dentro del bloque de rutas al agregar el Prompt Manager)
- **Archivo:** `command-center/server.js`
- **Resuelto por:** Claude Code

**Problema:** `const fs = require('fs')` aparecía en la línea 124, en medio de la definición de rutas Express. Aunque Node.js tolera `require()` en cualquier punto, es una convención crítica tenerlos al inicio. Si un módulo de la ruta superior fallara, `fs` no estaría declarado para las rutas inferiores.

```javascript
// ANTES — línea 124, en medio de rutas
const fs = require('fs');

// DESPUÉS — al inicio junto a los demás imports (líneas 1-8)
const fs = require('fs');
const path = require('path');
const express = require('express');
// ...
```

---

### BUG-013 — Falla silenciosa si `{context_text}` es eliminado del prompt

- **Fecha:** 2026-03-05
- **Severidad:** Importante (produce resultados AI fabricados sin ningún error)
- **Origen probable:** Posible edición accidental del Prompt Manager al borrar el placeholder
- **Archivo:** `agent/scout.py` — función `ask_ollama()`
- **Resuelto por:** Claude Code

**Problema:** Si el usuario editaba `prompt.txt` y eliminaba `{context_text}`, el `str.replace()` era un no-op. Ollama recibía el template vacío y generaba tendencias inventadas sin ningún error en los logs. El agente completaba con éxito reportando datos falsos.

```python
# AGREGADO — warning explícito antes del replace
if '{context_text}' not in base_prompt:
    print("⚠️  ADVERTENCIA: El prompt.txt no contiene {context_text}. Ollama no recibirá la data raspada.")
prompt = base_prompt.replace('{context_text}', context_text)
```

---

### BUG-014 — Variable `btnCancelPrompt` null (ID inexistente en el DOM)

- **Fecha:** 2026-03-05
- **Severidad:** Menor (código muerto, no causa crash visible)
- **Origen probable:** Antigravity (declaró la variable apuntando a un ID que nunca existió en el HTML)
- **Archivo:** `command-center/public/index.html`
- **Resuelto por:** Claude Code

**Problema:** `const btnCancelPrompt = document.getElementById('btnCancelPrompt')` retornaba `null`. El ID no existía en el HTML. Ningún `addEventListener` usaba esa variable. Código muerto eliminado.

---

### BUG-015 — Variable `btn` no definida en el click handler de Scout AI (ReferenceError crítico)

- **Fecha:** 2026-03-05
- **Severidad:** Critico (el botón principal del Command Center no hacía nada)
- **Origen probable:** Antigravity (renombró la variable de `btn` a `btnRunScout` en la declaración pero no actualizó las referencias internas del handler)
- **Archivo:** `command-center/public/index.html`
- **Resuelto por:** Claude Code

**Problema:** El click handler de Scout AI declaraba `const btnRunScout = document.getElementById('btnRunScout')` pero internamente usaba `btn.disabled`, `btn.textContent`, etc. `btn` nunca fue declarado — `ReferenceError` en la primera línea del handler, silencioso en consola, con el efecto de que el botón parecía no responder.

```javascript
// ANTES (en el handler)
btn.disabled = true;
btn.textContent = 'Ejecutando Scout...';

// DESPUÉS — referencias corregidas
btnRunScout.disabled = true;
btnRunScout.textContent = 'Ejecutando Scout...';
```

---

### BUG-016 — Todo el JavaScript anidado dentro del `if (btnRunScout)` (scope incorrecto)

- **Fecha:** 2026-03-05
- **Severidad:** Critico (todos los módulos JS del Command Center silenciosamente no funcionaban)
- **Origen probable:** Antigravity (al refactorizar el script, cerró la llave del bloque `if (btnRunScout)` en la línea incorrecta, dejando todos los módulos subsiguientes anidados dentro)
- **Archivo:** `command-center/public/index.html`
- **Resuelto por:** Claude Code — reescritura completa del bloque `<script>`

**Problema:** La estructura del JS era aproximadamente:
```javascript
document.addEventListener('DOMContentLoaded', () => {
  const btnRunScout = document.getElementById('btnRunScout');
  if (btnRunScout) {
    // Scout handler
    // fetchRadar()        ← anidado incorrectamente
    // addSource handler   ← anidado incorrectamente
    // deleteSource        ← anidado incorrectamente
    // banSource           ← anidado incorrectamente
    // btnGenerateReport   ← anidado incorrectamente
    // btnOpenPrompt       ← anidado incorrectamente
    // fetchUsers          ← anidado incorrectamente
  } // ← cierre del if, no del DOMContentLoaded
});
```

Si `btnRunScout` era `null` (o si cambiaba su ID), **todo lo demás dejaba de ejecutarse**. El Radar de Fuentes, los botones de Eliminar/Banear, el Generador de Reportes y el Editor de Prompts eran todos dependientes de que el botón Scout existiera en el DOM.

**Fix:** Reescritura completa con cada módulo como bloque independiente al nivel de `DOMContentLoaded`:
```javascript
document.addEventListener('DOMContentLoaded', () => {
  // === SCOUT AI ===
  const btnRunScout = document.getElementById('btnRunScout');
  if (btnRunScout) { btnRunScout.addEventListener(...); }

  // === RADAR DE FUENTES ===
  fetchRadar();
  // ...

  // === CONFIGURAR BUSQUEDA ===
  const btnOpenPrompt = document.getElementById('btnOpenPrompt');
  if (btnOpenPrompt) { btnOpenPrompt.addEventListener('click', () => window.open('/prompt-editor', '_blank')); }

  // === FÁBRICA DE REPORTES ===
  // ...
});
```

---

### FEATURE-003 — Fuentes predeterminadas de Digital Marketing + AI Marketing en `scout.py`

- **Fecha:** 2026-03-05
- **Desarrollado por:** Claude Code
- **Archivo:** `agent/scout.py`

**Cambio:** Cuando `get_pending_sources()` retorna una lista vacía (sin fuentes en Hostinger), el agente ahora usa `DEFAULT_SOURCES` (20 fuentes en 3 categorías) en lugar de abortar. Incluye EdTech (r/edtech, MIT, Stanford, Harvard), AI (r/artificial, VentureBeat), y Marketing Digital (r/marketing, HubSpot, Neil Patel, SEJ, SME, MarTech).

Adicionalmente, `agent/prompt.txt` fue actualizado para reflejar los tres dominios de Keiyi: EdTech, Marketing Digital con IA, y tendencias de contenido/SEO.

---

### FEATURE-004 — Sistema de Versionado de Prompts + Editor Dedicado

- **Fecha:** 2026-03-05
- **Desarrollado por:** Claude Code
- **Archivos:** `command-center/server.js` (4 endpoints nuevos), `agent/prompt_versions/` (directorio)

**Endpoints nuevos en `server.js`:**
- `GET /api/prompt/versions` — lista versiones con `daysLasted` calculado por diferencia entre timestamps consecutivos. La versión con mayor `daysLasted` es marcada `isMostEfficient: true`.
- `POST /api/prompt/restore/:filename` — valida nombre con regex, hace backup del prompt actual, copia la versión seleccionada a `prompt.txt`.
- `GET /prompt-editor` — sirve página HTML completa con textarea para editar el prompt y tabla de versiones con badge ⭐ y botones de restauración.
- `POST /api/prompt` actualizado para hacer backup automático antes de sobreescribir.

**Lógica de "versión más eficiente":**
```
daysLasted = (timestamp de la siguiente versión - timestamp de esta versión) en días
Si es la más reciente: (ahora - timestamp) en días
La que tenga mayor daysLasted = isMostEfficient = true
```

---

## TABLA RESUMEN ACUMULADA (actualizada)

| ID | Severidad | Origen | Archivo | Resuelto por | Fecha |
|---|---|---|---|---|---|
| BUG-001 | Critico | Antigravity + Gemini CLI | `User.php` | Antigravity | 2026-03-05 |
| BUG-002 | Critico | Antigravity + Gemini CLI | `UserResource.php` | Antigravity | 2026-03-05 |
| BUG-003 | Critico | Antigravity | `ScoutController.php` | Antigravity | 2026-03-05 |
| BUG-004 | Importante | Gemini CLI | `UserResource.php` | Antigravity | 2026-03-05 |
| BUG-005 | Importante | Antigravity | `AdminPanelProvider.php` | Claude Code | 2026-03-05 |
| BUG-006 | Critico | Antigravity | `.gitignore` | Claude Code | 2026-03-05 |
| BUG-007 | Importante | Antigravity | `server.js` | Claude Code | 2026-03-05 |
| BUG-008 | Critico | Antigravity | `scout_sources` ENUM | Claude Code | 2026-03-05 |
| BUG-009 | Critico | Claude Code | `AdminPanelProvider.php` | Antigravity + Claude Code | 2026-03-05 |
| BUG-010 | Importante | Claude Code | `index.html` | Claude Code | 2026-03-05 |
| BUG-011 | Menor | Antigravity | `server.js`, `scout.py` | Claude Code | 2026-03-05 |
| BUG-012 | Menor | Antigravity | `server.js` | Claude Code | 2026-03-05 |
| BUG-013 | Importante | Antigravity | `scout.py` | Claude Code | 2026-03-05 |
| BUG-014 | Menor | Antigravity | `index.html` | Claude Code | 2026-03-05 |
| BUG-015 | Critico | Antigravity | `index.html` | Claude Code | 2026-03-05 |
| BUG-016 | Critico | Antigravity | `index.html` | Claude Code | 2026-03-05 |
| ARCH-001 | — | Jefe/Antigravity | Arquitectura global | N/A (mandato) | 2026-03-05 |
| FEATURE-001 | — | Claude Code | `agent/scout.py` | N/A | 2026-03-05 |
| FEATURE-002 | — | Claude Code | Command Center | N/A | 2026-03-05 |
| FEATURE-003 | — | Claude Code | `agent/scout.py` | N/A | 2026-03-05 |
| FEATURE-004 | — | Claude Code | `server.js`, `prompt_versions/` | N/A | 2026-03-05 |

**Total bugs:** 16 | **Críticos:** 7 | **Importantes:** 4 | **Menores:** 3 | **Pendientes:** 0

---

### PATRON EMERGENTE — BUG-015 + BUG-016

Antigravity tiene una tendencia a introducir **bugs de scope y referencía en JS** al refactorizar el frontend. Dos señales de alerta recurrentes:
1. Renombrar variables sin actualizar todas las referencias internas del bloque (BUG-015)
2. Anidar módulos independientes dentro de bloques condicionales (BUG-016)

**Regla preventiva:** En cada entrega de `index.html`, Claude Code debe verificar que cada módulo JS (Scout, Radar, Report, Prompt, Users) esté al nivel de `DOMContentLoaded`, no anidado dentro de un `if (elemento)`.

---

---

## AUDITORÍA #5 — 2026-03-05

**Auditor:** Claude Code
**Disparador:** Regreso al búnker. Jefe reportó que el check SSH/SCP del Command Center "no funciona". Revisión de nuevas funcionalidades agregadas por Gemini CLI.
**Resultado:** 1 bug de UX crítico resuelto. Directiva 4 (⚠️ salud del Radar) completada.

---

### BUG-017 — Check SSH+SCP secuencial — endpoint congela la UI por hasta 10 segundos

- **Fecha:** 2026-03-05
- **Severidad:** Importante (UX rota — el botón parecía muerto)
- **Origen probable:** Gemini CLI (implementó los dos `exec` anidados en serie)
- **Archivo:** `command-center/server.js` (líneas 201-223)
- **Detectado por:** Claude Code
- **Resuelto por:** Claude Code

**Problema:** El endpoint `/api/check-hostinger` ejecutaba SSH y luego, dentro de su callback, ejecutaba SCP. Con `ConnectTimeout=5` en cada comando, si el servidor no respondía, el endpoint tardaba hasta **10 segundos** en responder. Durante ese tiempo el botón mostraba `...` y no actualizaba la UI. El usuario lo interpretaba como botón roto.

```javascript
// ANTES — ejecución serial (hasta 10s si ambos timeoutean)
exec(sshCheck, (sshErr) => {
    exec(scpCheck, (scpErr) => {  // solo arranca cuando SSH termina
        res.json({ ... });
    });
});

// DESPUÉS — ejecución paralela (máximo 5s, el que tarde más)
const runCheck = (cmd) => new Promise((resolve) => {
    exec(cmd, { timeout: 8000 }, (err) => resolve(!err));
});
Promise.all([runCheck(sshCheck), runCheck(scpCheck)]).then(([sshStatus, scpStatus]) => {
    res.json({ ... });
});
```

**Mejoras adicionales del fix:**
- `timeout: 8000` en cada `exec` previene que un proceso SSH colgado congele el servidor indefinidamente
- La latencia mostrada ahora refleja el tiempo real de ambas pruebas en paralelo, no la suma

---

### FEATURE-005 — Directiva 4 completada: ⚠️ Monitor de Salud en Radar de Fuentes

- **Fecha:** 2026-03-05
- **Desarrollado por:** Claude Code
- **Archivo:** `command-center/public/index.html` (función `fetchRadar`)

**Contexto:** Gemini CLI había solicitado en la Directiva 4 un icono de advertencia cuando una fuente falla. El modelo `ScoutSource` no tiene un campo `last_scrape_status` por fuente — el `scrape_status` que Antigravity implementó en `scout.py` va al `ScoutInsight` (reporte global), no a fuentes individuales. Se usó el campo existente `relevance_score` como proxy de salud: si es < 50, la fuente es de bajo rendimiento.

```javascript
// AGREGADO en fetchRadar(), antes de construir el innerHTML de cada fila
const healthIcon = source.relevance_score < 50
    ? ' <span title="Fuente de bajo rendimiento — considera eliminarla">⚠️</span>'
    : '';

// El ⚠️ aparece inline en la celda del nombre de la fuente
`<td ...>📌 ${source.name}${healthIcon}</td>`
```

**Nota para Antigravity:** Si en el futuro se quiere ⚠️ por fallo real de scraping (no por score), se necesita una migración que agregue `last_scrape_status VARCHAR` a `scout_sources` y que `scout.py` lo actualice por fuente. Eso es Fase 6+ trabajo.

---

---

### BUG-018 — Conexión SSH/SCP a Hostinger falla — PENDIENTE DE DIAGNÓSTICO

- **Fecha:** 2026-03-05
- **Severidad:** Importante
- **Origen probable:** Configuración de Hostinger (clave SSH no autorizada, puerto incorrecto, o deploy no realizado aún)
- **Archivo:** `command-center/server.js` (endpoint `/api/check-hostinger`)
- **Detectado por:** Jefe (prueba en vivo)
- **Resuelto por:** Claude Code (2026-03-05)

**Causa raíz:** El servidor Node.js nunca fue reiniciado después del fix de paralelismo (BUG-017). Seguía corriendo el código viejo con la ejecución serial. La conexión SSH a Hostinger era válida desde el principio — verificado con `ssh -v` que retornó `Authenticated using "publickey"` y `Exit status 0` en 0.3 segundos.

**Resolución:** `pkill -f "node server.js"` + reinicio del servidor. El botón ⚡ Test pasó a mostrar SSH: ONLINE / SCP: ONLINE correctamente.

---

## TABLA RESUMEN ACUMULADA (actualizada)

| ID | Severidad | Origen | Archivo | Resuelto por | Fecha |
|---|---|---|---|---|---|
| BUG-001 al BUG-016 | Varios | Varios | Varios | Varios | 2026-03-05 |
| BUG-017 | Importante | Gemini CLI | `server.js` | Claude Code | 2026-03-05 |
| BUG-018 | Importante | Operacional | Node.js sin reiniciar | Claude Code | 2026-03-05 |
| FEATURE-005 | — | Claude Code | `index.html` | N/A | 2026-03-05 |

**Total bugs:** 18 | **Críticos:** 7 | **Importantes:** 6 | **Menores:** 3 | **Pendientes:** 0
**Directivas completadas:** 1, 2, 3, 4 ✅

---

---

## AUDITORÍA #6 — 2026-03-06

**Auditor:** Claude Code
**Disparador:** Sesión de desarrollo intensivo. Nuevas features entregadas: Deep Scout, Portal Academia, Google Drive Sync.
**Resultado:** 0 bugs nuevos. 4 features entregadas. Sistema de investigación operativo.

---

### FEATURE-006 — Deep Scout AI (Reddit Intelligence Crawler)

- **Fecha:** 2026-03-06
- **Desarrollado por:** Claude Code
- **Archivos:** `agent/deep_scout.py`, `agent/DEEP_SCOUT_TECHNIQUES.md`, `agent/deep_sources.json`, `agent/research_db.json`, `agent/scraped_ids.json`

**Descripción:** Sistema de inteligencia profunda que escarba Reddit en 3 dimensiones (top/semana + new + controversial/mes) extrayendo herramientas recomendadas, preguntas frecuentes y referencias externas. Anti-duplicados por post ID. Arquitectura de técnicas documentada y experimentable.

**Técnica activa:** T2 Batch x5 — 5 posts por llamada Ollama (5x más rápido que T1 per-post).
**Ver:** `agent/DEEP_SCOUT_TECHNIQUES.md` para comparativa de técnicas T1→T4.

---

### FEATURE-007 — Sección Deep Scout en Command Center

- **Fecha:** 2026-03-06
- **Desarrollado por:** Claude Code
- **Archivos:** `command-center/server.js` (endpoints 10-14), `command-center/public/index.html`

**Endpoints nuevos:**
- `GET/POST/DELETE /api/deep-sources` — CRUD de subreddits a escarbar
- `POST /api/run-deep-scout` — ejecuta `deep_scout.py`
- `GET /api/research-intel` — devuelve inteligencia acumulada (top tools/questions/refs)

**UI nueva:** Sección "Fuentes Profundas (Reddit)" + sección "Inteligencia de Academia" con 3 columnas en tiempo real.

---

### FEATURE-008 — Google Drive Auto-Sync (Local Desktop, sin API)

- **Fecha:** 2026-03-06
- **Desarrollado por:** Claude Code
- **Archivo:** `agent/google_drive_uploader.py`

**Decisión técnica:** Se intentó Service Account API → bloqueado por Google (sin cuota de almacenamiento para SA en My Drive). Solución adoptada: escritura directa a la carpeta local de Google Drive Desktop (`~/Library/CloudStorage/GoogleDrive-*/My Drive/gemini/Keiyi Scout Intelligence`). Google Drive Desktop sincroniza automáticamente. Cero dependencias adicionales, cero API keys, funciona siempre que Drive Desktop esté activo.

**Archivos generados en cada run de Deep Scout:**
- `intel_report_YYYY-MM-DD.txt` — reporte legible formateado para NotebookLM
- `research_db_latest.json` — JSON crudo para procesamiento futuro

**Integración:** `deep_scout.py` llama a `upload_to_drive()` automáticamente al final de cada análisis.

---

### FEATURE-009 — Portal de Alumnos `/academia`

- **Fecha:** 2026-03-06
- **Desarrollado por:** Claude Code
- **Archivos:** `app/Http/Controllers/AcademiaController.php`, `resources/views/academia/dashboard.blade.php`, `routes/web.php`

**Protección:** Middleware `auth` + `approved` — solo alumnos aprobados acceden.
**Diseño:** Neo-brutalist "Pop" de Keiyi (Space Grotesk, lima #a3e635, amarillo #facc15, bordes negros).
**Contenido:** Hero de bienvenida personalizado, stats de progreso en tiempo real, 4 tarjetas de talleres (Taller 0, 1, 2, Marketing Elite) con badge "Próximamente". Listo para activar cursos en Fase 6.

---

## TABLA RESUMEN ACUMULADA (actualizada)

| ID | Severidad | Origen | Archivo | Resuelto por | Fecha |
|---|---|---|---|---|---|
| BUG-001 al BUG-017 | Varios | Varios | Varios | Varios | 2026-03-05 |
| BUG-018 | Importante | Operacional | Node.js sin reiniciar | Claude Code | 2026-03-06 |
| FEATURE-006 | — | Claude Code | `agent/deep_scout.py` | N/A | 2026-03-06 |
| FEATURE-007 | — | Claude Code | `server.js` + `index.html` | N/A | 2026-03-06 |
| FEATURE-008 | — | Claude Code | `agent/google_drive_uploader.py` | N/A | 2026-03-06 |
| FEATURE-009 | — | Claude Code | Portal Academia | N/A | 2026-03-06 |

**Total bugs:** 18 | **Todos resueltos** ✅
**Features entregadas esta sesión:** 4 (006-009)

---

*Siguiente sesión: Fase 6 — contenido real de los talleres + migración `last_scrape_status` + experimento T3 Deep Scout.*

---

## AUDITORÍA #7 — 2026-03-21

**Auditor:** Claude Code
**Disparador:** Revisión completa del proyecto — código Laravel, agentes Python, Command Center Node.js, scripts de deploy.
**Resultado:** 42 issues encontrados (4 críticos, 5 altos, 7 medios, resto bajos). 6 fixes aplicados directamente.

---

### BUG-019 — XSS en blog: contenido HTML sin sanitizar

- **Fecha:** 2026-03-21
- **Severidad:** Crítico
- **Origen probable:** Diseño original del blog pipeline
- **Archivo(s):** `resources/views/blog/show.blade.php:81`, `app/Models/Post.php`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~5 min

**Problema:** `{!! $post->content !!}` renderizaba HTML crudo sin sanitizar. El contenido viene de William (IA) y del editor admin, pero si cualquiera de esas fuentes inyectara `<script>`, se ejecutaría en el navegador del visitante.

**Fix:** Sanitización en dos capas:
1. **Modelo:** `Post::sanitizeHtml()` — strip_tags con whitelist de tags seguros + eliminación de atributos `on*` y URLs `javascript:`. Se ejecuta automáticamente en `creating` y `updating`.
2. **Vista:** Doble protección con `Post::sanitizeHtml($post->content)` en la plantilla para contenido existente en BD.

---

### BUG-020 — Command injection en /api/deep-dive/:subreddit

- **Fecha:** 2026-03-21
- **Severidad:** Crítico
- **Origen probable:** Claude Code (Feature-006, Deep Scout)
- **Archivo(s):** `command-center/server.js:158-167`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~3 min

**Problema:** El parámetro `:subreddit` de la URL se pasaba directamente a `exec()` sin validación ni escapeo. Un atacante podía inyectar comandos shell.

**Fix:** Validación regex `[a-zA-Z0-9_]` + reemplazo de `exec()` por `execFile()` con argumentos como array.

---

### BUG-021 — PHP injection en runPHP (escapeo insuficiente)

- **Fecha:** 2026-03-21
- **Severidad:** Crítico
- **Origen probable:** Diseño original del Command Center
- **Archivo(s):** `command-center/server.js:92-108`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~3 min

**Problema:** El escapeo de input para interpolación PHP solo reemplazaba `'` con `\'`. Un atacante podía inyectar backslashes para escapar el escapeo y ejecutar código PHP arbitrario.

**Fix:** Función `phpEscape()` que escapa backslashes, comillas simples, comillas dobles y signos `$` en el orden correcto. Response usa valor del modelo en vez de re-interpolar input del usuario.

---

### BUG-022 — API de posts sin autorización por rol

- **Fecha:** 2026-03-21
- **Severidad:** Alto
- **Origen probable:** Diseño original del blog editorial pipeline
- **Archivo(s):** `app/Http/Controllers/Api/PostApiController.php:20-39`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~2 min

**Problema:** Los endpoints `approve`, `publish` y `reject` solo requerían un token Sanctum válido. Cualquier usuario autenticado podía gestionar posts del blog.

**Fix:** Método privado `authorizeAdmin()` que verifica `role === 'super-admin'` antes de cada acción mutativa. Devuelve 403 si no es admin.

---

### BUG-023 — Quiz data sin validación de estructura

- **Fecha:** 2026-03-21
- **Severidad:** Medio
- **Origen probable:** Diseño original del LMS
- **Archivo(s):** `app/Http/Controllers/CourseController.php:101-123`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~2 min

**Problema:** El campo `quiz_data` (JSON) se iteraba sin verificar que fuera un array ni que cada pregunta tuviera las keys requeridas (`question`, `options`, `correct`). JSON malformado causaba error 500.

**Fix:** Validación `is_array()` en guard clause + `continue` para preguntas con estructura incompleta.

---

### BUG-024 — API sin rate limiting

- **Fecha:** 2026-03-21
- **Severidad:** Medio
- **Origen probable:** Omisión en diseño de rutas API
- **Archivo(s):** `routes/api.php:12`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~1 min

**Problema:** Los endpoints de gestión de posts (`/api/posts/*`) no tenían rate limiting.

**Fix:** Middleware `throttle:60,1` (60 requests/minuto) agregado al grupo de rutas.

---

### Resumen Auditoría #7

| Bug | Severidad | Origen | Archivo | Resuelto |
|-----|-----------|--------|---------|----------|
| BUG-019 | Crítico | Blog pipeline | Post.php, show.blade.php | 2026-03-21 |
| BUG-020 | Crítico | Feature-006 | server.js:158-167 | 2026-03-21 |
| BUG-021 | Crítico | Command Center | server.js:92-108 | 2026-03-21 |
| BUG-022 | Alto | Blog API | PostApiController.php | 2026-03-21 |
| BUG-023 | Medio | LMS | CourseController.php | 2026-03-21 |
| BUG-024 | Medio | API routes | api.php:12 | 2026-03-21 |

**Issues pendientes documentados (no aplicados):**
- Credenciales SSH hardcodeadas en `deploy.sh` y `server.js` — mover a env vars
- Sin soft deletes en modelo `User` — hard delete huerfanea enrollments
- Modelo `BlogPost.php` duplicado/sin uso — eliminar
- Tabla `clients` vacía — eliminar o poblar
- Filament Resources sin autorización granular por recurso
- `AcademiaController` usa `DB::table()` en vez de Eloquent
- Input sin sanitizar en `World3DController::requestOrder` (concatenación directa)

### BUG-025 — Test de registro desactualizado tras agregar campos

- **Fecha:** 2026-03-21
- **Severidad:** Menor
- **Origen probable:** Commit `a24366e` (privacy policy + registration consent)
- **Archivo(s):** `tests/Feature/Auth/RegistrationTest.php:21-26`
- **Detectado por:** Claude Code (Auditoría #7)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~2 min

**Problema:** El test `new users can register` enviaba el payload original de Breeze sin los campos `apellido_paterno` y `accepts_terms` que se agregaron al formulario de registro. La validación rechazaba el request y el usuario nunca se autenticaba → `assertAuthenticated()` fallaba.

**Fix:** Agregados los campos faltantes al payload del test:

```php
// Antes:
'name' => 'Test User',
'email' => 'test@example.com',
'password' => 'password',
'password_confirmation' => 'password',

// Después:
'name' => 'Test',
'apellido_paterno' => 'User',
'email' => 'test@example.com',
'password' => 'password',
'password_confirmation' => 'password',
'accepts_terms' => '1',
```

**Suite completo:** 25/25 tests pasan (61 assertions).

---

### BUG-026 — Usuarios pueden completar lecciones sin estar inscritos

- **Fecha:** 2026-03-21
- **Severidad:** Medio
- **Origen probable:** Diseño original del LMS
- **Archivo(s):** `app/Http/Controllers/CourseController.php:69-93, 95-141`
- **Detectado por:** Claude Code (Auditoría #7, segunda pasada)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~3 min

**Problema:** Los métodos `markComplete()` y `submitQuiz()` no verificaban que el usuario estuviera inscrito en el curso. Un usuario autenticado podía POST a cualquier lección y marcarla como completada o enviar quiz sin estar inscrito.

**Fix:** Método privado `ensureEnrolled()` que verifica enrollment y aborta con 403 si no existe. Agregado a ambos métodos antes de la lógica de completado.

---

### BUG-027 — PrintOrder permite user_id NULL en ruta pública

- **Fecha:** 2026-03-21
- **Severidad:** Medio
- **Origen probable:** Diseño original de 3D World
- **Archivo(s):** `routes/web.php:31`, `database/migrations/2026_03_07_053358_create_3d_world_tables.php:31`
- **Detectado por:** Claude Code (Auditoría #7, segunda pasada)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~3 min

**Problema:** La ruta `POST /3d-world/order/{item}` es pública (sin middleware `auth`) — por diseño, para que visitantes puedan solicitar cotización. Pero `auth()->id()` retorna `null` y la columna `user_id` tenía constraint `NOT NULL` con foreign key, causando error SQL en producción.

**Fix:** Migración `2026_03_22_041927_make_print_orders_user_id_nullable.php` — hace `user_id` nullable para aceptar órdenes de usuarios no autenticados.

---

### BUG-028 — Lesson content_html sin sanitización XSS

- **Fecha:** 2026-03-21
- **Severidad:** Medio
- **Origen probable:** Diseño original del LMS
- **Archivo(s):** `app/Models/Lesson.php`, `resources/views/academia/course/lesson.blade.php:810`
- **Detectado por:** Claude Code (Auditoría #7, segunda pasada)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~2 min

**Problema:** `{!! $lesson->content_html !!}` renderizaba HTML sin sanitizar. Aunque el contenido viene del admin vía Filament RichEditor, si la fuente se contaminaba, se ejecutaría JavaScript arbitrario.

**Fix:** Hooks `creating` y `updating` en el modelo `Lesson` que llaman a `Post::sanitizeHtml()` para limpiar `content_html` antes de guardar. Reutiliza el sanitizador centralizado del modelo Post.

---

### BUG-029 — SSRF en FilamentInventory::scrapeFromUrl()

- **Fecha:** 2026-03-21
- **Severidad:** Medio
- **Origen probable:** Feature de auto-scraping de precios
- **Archivo(s):** `app/Models/FilamentInventory.php:42-49`
- **Detectado por:** Claude Code (Auditoría #7, segunda pasada)
- **Resuelto por:** Claude Code en 2026-03-21
- **Tiempo del fix:** ~3 min

**Problema:** `scrapeFromUrl()` aceptaba cualquier URL y hacía request HTTP sin restricciones. Un admin (o atacante con acceso al panel) podía pasar URLs internas como `http://localhost:9200` o `http://169.254.169.254` (AWS metadata) para explorar la red interna.

**Fix:** Constante `ALLOWED_SCRAPE_DOMAINS` con whitelist de dominios permitidos (Amazon, MercadoLibre). Validación por dominio antes de hacer request. URLs no permitidas retornan datos vacíos con nota explicativa.

---

### CLEANUP-001 — Eliminación de modelo y controlador muertos

- **Fecha:** 2026-03-21
- **Archivo(s) eliminados:** `app/Models/BlogPost.php`, `app/Http/Controllers/BlogPostController.php`
- **Detectado por:** Claude Code (Auditoría #7, segunda pasada)

**Descripción:** `BlogPost.php` era un modelo legacy que referenciaba la tabla `blog_posts`, pero el sistema de blog actual usa el modelo `Post.php` con la tabla `posts`. El controlador `BlogPostController.php` estaba completamente vacío (solo la clase sin métodos). Ninguno tenía rutas activas.

---

### Resumen final Auditoría #7

| Bug | Severidad | Archivo | Resuelto |
|-----|-----------|---------|----------|
| BUG-019 | Crítico | Post.php, show.blade.php | 2026-03-21 |
| BUG-020 | Crítico | server.js:158-167 | 2026-03-21 |
| BUG-021 | Crítico | server.js:92-108 | 2026-03-21 |
| BUG-022 | Alto | PostApiController.php | 2026-03-21 |
| BUG-023 | Medio | CourseController.php | 2026-03-21 |
| BUG-024 | Medio | api.php:12 | 2026-03-21 |
| BUG-025 | Menor | RegistrationTest.php | 2026-03-21 |
| BUG-026 | Medio | CourseController.php | 2026-03-21 |
| BUG-027 | Medio | print_orders migration | 2026-03-21 |
| BUG-028 | Medio | Lesson.php | 2026-03-21 |
| BUG-029 | Medio | FilamentInventory.php | 2026-03-21 |
| CLEANUP-001 | — | BlogPost.php, BlogPostController.php | 2026-03-21 |

**Issues pendientes documentados (no aplicados):**
- Credenciales SSH hardcodeadas en `deploy.sh` y `server.js` — mover a env vars
- Sin soft deletes en modelo `User` — hard delete huerfanea enrollments
- Tabla `clients` vacía — eliminar o poblar
- Filament Resources sin autorización granular por recurso
- `AcademiaController` usa `DB::table()` en vez de Eloquent
- Enrollment usa slug como FK en vez de ID numérico — fragilidad si cambia slug
- `TallerController` auto-marca `is_3d_client = true` sin verificación

**Total bugs resueltos esta sesión:** 12 | **Pendientes:** 7
**Suite de tests:** 25/25 (61 assertions)
