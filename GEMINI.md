# KEIYI DIGITAL: PROTOCOLO DE MEMORIA UNIFICADA (v18.5)
Este archivo es una colaboración entre **Antigravity (Orquestador)** y **Gemini CLI (Ingeniero)**. Ningún agente debe sobrescribir las secciones del otro sin validación previa.

---

## 🧠 SECCIÓN GEMINI CLI: Implementación y Lógica
### Hitos del 4 de Marzo, 2026 (Sesión II):
- **Protocolo de Seguridad:** Se ha creado `legacy_backup/` y se han movido todos los activos estáticos y Markdown para proteger los 6 cursos desarrollados.
- **Preparación para Laravel Profesional:** Sincronización con Antigravity para implementar el esquema modular (LMS + CRM + Scout AI).
- **Bloqueo Técnico:** Pendiente de localizar `php` y `composer` en el entorno local para ejecutar el scaffold oficial.

### Hitos del 7 de Marzo, 2026 (Transición a UI Nativa):
- **Dashboard UI (HTML):** Prototipo (`demo_sidebar.html`) finalizado con paneles de Límites Hardware (16GB RAM M2, Hostinger EP limits).
- **Iniciativa Swift Scaffold:** Debido a que Claude Code agotó sus tokens, Gemini/Antigravity asume la creación del esqueleto inicial de la App Nativa (`native-agent/KeiyiAgentApp.swift` y vistas principales) basado en el layout aprobado del Sidebar. Claude retomará para la integración del módulo `Foundation/Process`.


---

## 🤖 SECCIÓN ANTIGRAVITY: Misión y Orquestación
- **Rol:** Plataforma "Agent-first" de desarrollo. Gestión de estados, despliegue de agentes especializados y auditoría de procesos.
- **Flujo Colaborativo:** Antigravity define la arquitectura global y Gemini CLI ejecuta la implementación táctica en el sistema de archivos.
- **Estado del Sistema:** Sincronización activa con Gemini CLI para la expansión de la Keiyi Academy.

---

## 🧠 SECCIÓN GEMINI CLI: Implementación y Lógica
### Hitos del 4 de Marzo, 2026:
- **Despliegue de Keiyi Academy:** 6 Talleres Maestros completados.
    1. **IA Origins:** Historia, modelos y ingeniería de prompts R-C-T-F.
    2. **Notion Mastery:** Wikis, Proyectos y Agentes IA (Basado en Notion.com).
    3. **Marketing Elite:** Ventas automatizadas (ManyChat + Make + Claude).
    4. **Viral Content:** Metodología PGR y Edición IA.
    5. **3D World:** Prototipado e Impresión 3D (Icono Benchy).
    6. **Productividad Pro:** OS Aumentado y Día de 4 Horas.
- **Arquitectura LMS:** Implementación de Middleware de aprobación (`CheckApproved`) y Portal de Gestión de Alumnos en Laravel 11.
- **Arquitectura Local-First (API-Less):** El "Command Center" (Node.js) y la Inteligencia Artificial (Scout) corren exclusivamente en la Mac M2. Se HAN CERRADO las APIs públicas en Hostinger. Toda comunicación (Rastreo, Aprobación de Usuarios, Exportación de Reportes) ocurre mediante túneles SSH ejecutando PHP Raw y transferencias SCP directamente a la Base de Datos, garantizando latencia cero y máxima seguridad perimetral.
- **Diseño Visual:** DNA Keiyi integrado (Bordes Pop, Sombras duras, Hand-notes).

### Hitos del 8 de Marzo, 2026 (La Trifuerza Cognitiva):
- **Arquitectura Operativa de Agentes:** Se separaron oficialmente las labores de inteligencia para evitar "Fatiga de Contexto" y cuidar la RAM del M2.
  - **Perry (Radar - A lo ancho):** Busca *nuevas señales* de vida en internet y categoriza sitios por dinamismo. Genera el mapa para los de abajo.
  - **Dipper (Analista - A lo profundo):** Únicamente lee lo que Perry le señala. Se enfoca en threads calientes e hilos largos, usando hashing MD5 para no repetir. Genera puros Insights/JSON.
  - **William (Redactor - Ejecución):** No busca nada. Toma la materia prima de Dipper y redacta borradores para aprobación humana.
- **Reparación Analítica:** Modificado el código en `perry.py` para utilizar Regex y extraer limpiamente los JSON provenientes de `claude` CLI y `gemini` CLI.

---

## 📜 HISTORIAL HEREDADO (Detallado)
- **VPN-Watch (Python):** Script continuo para asegurar IP en USA. Incluye logging en JSON Lines (`vpn-watch.out.log`), reportes diarios automatizados vía `launchd` (`reporter.py`), y Dashboard en Flask (puerto 5001) para visualizar fiabilidad y capturas de monitoreo.
- **OkVisa.mx (Laravel):**
    *   Implementación de Wizard de toma de datos con validación y conversión a mayúsculas automática.
    *   Sistema de códigos de acceso con contador de usos (máx 3).
    *   Panel administrativo para gestión de clientes y generación de códigos.
    *   Integración de Chatbot Gemini con base de conocimientos en `config/chatbot_knowledge.php`.
- **Ender-3 & OctoPrint:**
    *   Actualización a placa silenciosa Creality V4.2.7 y firmware Marlin comunitario.
    *   Integración de BLTouch con calibración de Probe Offset y visualización de malla en OctoPrint (Bed Visualizer).
- **Voice-IoT Node (DIY Alexa):** Arquitectura de dos dispositivos. "Brain" en Mac M2 (WebSocket server, Ollama/gemma3, faster-whisper, Piper TTS) y "Ear" en RPi Zero 2 W (Wyoming Satellite).

---

## 🏗️ INFRAESTRUCTURA Y CREDENCIALES
- **Entorno Local (Mac - anuarlv):** PHP 8.3 (vía Homebrew) / Composer 2.9 / Laravel 11.48.
- **Servidor de Producción (Hostinger):**
    *   **IP:** 185.212.70.24 | **Puerto SSH:** 65002 | **Usuario:** u129237724
    *   **Stack:** PHP 8.3.25 | Laravel Framework 11.47.0
    *   **Directorio raíz de app:** `domains/keiyi.digital/laravel_app`
- **Llaves y Seguridad:**
    *   **Llave Privada SSH:** `/Users/anuarlv/.ssh/id_rsa` (Usar esta ruta para cualquier conexión con Hostinger).

---

## 🛠️ MANDATOS OPERATIVOS ACTUALIZADOS
1. **Mandato de Paridad de Producción:** PROHIBIDO instalar software o librerías que requieran:
    * Acceso `sudo` o privilegios de Root.
    * Servicios persistentes (Redis, Supervisor, Meilisearch, Laravel Echo Server).
    * Versiones de PHP superiores a la **8.3.x**.
    * Cualquier dependencia que no pueda ser gestionada vía `composer` o `npm` estándar.
2. **Control de Versiones:** El uso de **GIT** es mandatorio. Cada hito debe cerrarse con un commit descriptivo.
3. **Coherencia Visual:** Priorizar Safari como navegador de prueba y estilo "Pop Digital" de Keiyi.
4. **Seguridad:** Los recursos (`academy_resources/`) son privados y se sirven vía controlador.
5. **Colaboración:** Respetar los archivos `MASTER_SOURCE.md` y `CONTEXT_MEMORY.md` como puentes entre agentes.

### 📡 CANALES OFICIALES DE COMUNICACIÓN INTER-AGENCIAS
Todos los agentes (Antigravity, Claude Code, Gemini CLI) DEBEN respetar estrictamente el propósito de los siguientes archivos:
*   **`AGENT_COMMUNICATION.md`**: Bandeja de entrada/salida para mandatos directos y órdenes de ejecución técnica entre los miembros del enjambre.
*   **`KEIYI_RESEARCH_LAB.md`**: El laboratorio para debatir arquitectura, discutir ideas largas o planificar refactorizaciones antes de tocar código fuente.
*   **`PROTOCOL_AGENTS.md`**: Establece el reglamento y flujo de datos de los agentes (Dipper y William) del Búnker Táctico.
*   **`GEMINI.md`**: El cerebro e historial duro compartido (System Prompt Injected). ÚNICAMENTE para asentar hitos terminados y lineamientos operacionales clave, no ideas al aire.

---
**Usuario Mac:** anuarlv | **Contexto:** Keiyi Digital Agency 2026.
