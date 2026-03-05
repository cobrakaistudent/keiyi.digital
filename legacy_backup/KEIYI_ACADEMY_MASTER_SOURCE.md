# KEIYI ACADEMY: MASTER SOURCE PACK (v3.0 - Full Ecosystem Ready)
Este documento es la fuente de verdad definitiva para el desarrollo de la Keiyi Academy. Contiene la inteligencia de mercado, el currículum completo de 6 talleres y las guías técnicas de implementación.

---

## 1. PROYECTO: KEIYI ACADEMY (v18.5)
**Objetivo:** LMS (Learning Management System) dinámico integrado en keiyi.digital para transformar profesionales en entidades de alto rendimiento mediante IA y Estrategia de Élite.

### Identidad de Marca (Visual DNA):
*   **Estilo:** "Pop Digital" / "Funky Tech".
*   **Elementos:** Bordes negros gruesos (4px), sombras duras (hard shadows), fuentes manuscritas para notas (`Gloria Hallelujah`), fondos cálidos (`#FFF5EB`) y subrayados amarillos (`#FFD700`).
*   **Iconografía Aprobada:** 
    *   IA Origins: Universo / Galaxia.
    *   Notion Mastery: Logotipo oficial 'N' de Notion.com.
    *   3D World: Cabezal de impresora 3D trabajando / Barquito Benchy.
    *   Viral Contenido: Logos 3D de redes sociales (Instagram, TikTok, FB, YT).

---

## 2. CURRÍCULUM COMPLETO (6 TALLERES MAESTROS)

### 2.1 IA Origins: De Zero a Power User (Fundacional)
*   **Foco:** Historia (de ChatGPT a Claude/Gemini), anatomía de LLMs, alucinaciones y herramientas 100% gratuitas.
*   **Ingeniería de Prompts:** Fórmula **R-C-T-F** (Rol, Contexto, Tarea, Formato).
*   **Entregable:** Asesor Financiero / Chef Nutricionista personalizado.

### 2.2 Notion Mastery: Tu Segundo Cerebro (Productividad)
*   **Foco:** Wikis, Proyectos, Bases de Datos Relacionales y Agentes IA nativos.
*   **Novedades 2026:** Notion Home, Notion Calendar y Notion Sites.
*   **Metodología:** Sistema P.A.R.A. (Tiago Forte) aplicado a Notion.

### 2.3 Marketing Elite: Sistemas de Profit (Estrategia)
*   **Foco:** Diagnóstico de competencia con Perplexity, contenido HITL (Human-in-the-loop) y el "Clon de Ventas" (ManyChat + Make + Claude 3.5).
*   **Pauta Digital:** Segmentación Invisible y Pruebas A/B Masivas (DCO).

### 2.4 Viral Contenido: Fábrica de Reels (Viralidad)
*   **Foco:** Método PGR (Punch-Gap-Reward), ganchos de 1.5s, edición IA (Submagic/Captions) y TikTok SEO.

### 2.5 3D World: Prototipado con IA (Innovación)
*   **Foco:** De Idea a Malla 3D (Midjourney -> Luma AI), Texturizado PBR e Impresión 3D real (Slicing/Benchy).

### 2.6 Productividad Pro: El Día de 4 Horas (Eficiencia)
*   **Foco:** Optimización del OS (Mac/Win), Inbox Zero con Copilots y Agenda Dinámica gestionada por IA.

---

## 3. ARQUITECTURA TÉCNICA (LARAVEL 11)

### Sistema de Acceso:
1.  **Registro:** El alumno se registra en `/register`.
2.  **Estado Inicial:** `is_approved = false` (bloqueado por Middleware `CheckApproved`).
3.  **Aprobación de Admin:** El administrador gestiona alumnos en `/admin/academy/students`.
4.  **Acceso Total:** Una vez aprobado, el Dashboard (`/academy`) habilita los 6 cursos.

### Gestión de Contenidos:
*   **Markdown Dinámico:** Las lecciones se leen directamente de `academy_content/` mediante el `AcademyController`.
*   **Doble Formato:** Cada lección incluye **Guía de Estudio** (Lectura) y **Script de Vídeo** (30-45 min).
*   **Recursos Seguros:** Descargas protegidas en `academy_resources/` vía controlador (no públicas).

---

## 4. MASTER PROMPTS (SINOPSIS)
*   **Ingeniería Inversa:** "Analiza el post de mi competidor... extrae hooks emocionales... identifica brechas."
*   **Escritura HITL:** "Dame el esqueleto, deja espacios para [MI ANÉCDOTA], optimiza el músculo final."
*   **Ventas Senior:** "Actúa como Director de Ventas... califica el lead... invita a agendar solo si califica."
