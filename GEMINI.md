# KEIYI DIGITAL: PROTOCOLO DE MEMORIA UNIFICADA (v18.5)
Este archivo es una colaboración entre **Antigravity (Orquestador)** y **Gemini CLI (Ingeniero)**. Ningún agente debe sobrescribir las secciones del otro sin validación previa.

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

## 🛠️ MANDATOS OPERATIVOS ACTUALIZADOS
1. **Control de Versiones:** El uso de **GIT** es mandatorio. Cada hito debe cerrarse con un commit descriptivo.
2. **Coherencia Visual:** Priorizar Safari como navegador de prueba y estilo "Pop Digital" de Keiyi.
3. **Seguridad:** Los recursos (`academy_resources/`) son privados y se sirven vía controlador.
4. **Colaboración:** Respetar los archivos `MASTER_SOURCE.md` y `CONTEXT_MEMORY.md` como puentes entre agentes.

---
**Usuario Mac:** anuarlv | **Contexto:** Keiyi Digital Agency 2026.
