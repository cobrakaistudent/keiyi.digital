# KEIYI DIGITAL: PROTOCOLO DE MEMORIA UNIFICADA (v18.5)
Este archivo es una colaboración entre **Antigravity (Orquestador)** y **Gemini CLI (Ingeniero)**. Ningún agente debe sobrescribir las secciones del otro sin validación previa.

---

## 🧠 SECCIÓN GEMINI CLI: Implementación y Lógica
### Hitos del 4 de Marzo, 2026 (Sesión II):
- **Protocolo de Seguridad:** Se ha creado `legacy_backup/` y se han movido todos los activos estáticos y Markdown para proteger los 6 cursos desarrollados.
- **Preparación para Laravel Profesional:** Sincronización con Antigravity para implementar el esquema modular (LMS + CRM + Scout AI).
- **Bloqueo Técnico:** Pendiente de localizar `php` y `composer` en el entorno local para ejecutar el scaffold oficial.

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

---
**Usuario Mac:** anuarlv | **Contexto:** Keiyi Digital Agency 2026.
