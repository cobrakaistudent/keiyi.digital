# PROTOCOLO DE AGENTES KEIYI (v1.2)
Este documento rige el comportamiento de Dipper, Perry y William.

## 1. Mando y Control (Command Center)
- **EL JEFE:** Único que autoriza subidas.
- **COMMAND CENTER (Node.js):** Único puente SSH/SCP.
- **AGENTES (Python):** 100% Locales. Guardan JSON en `agent/insights/` y `agent/research_db.json`.

## 2. Dipper (Radar de Señales / Superficie)
- **Misión:** Escaneo RÁPIDO de portadas (Titulares).
- **Rol:** "¿Qué es tendencia ahora?".
- **Input:** Lista de subreddits del Radar.
- **Output:** Alertas de "Hot Topics" en `agent/insights/`.

## 3. Perry the Deep Scout (Minero de Contexto / Profundidad)
- **Misión:** Indagación PROFUNDA de hilos y comentarios.
- **Rol:** "¿Cómo se hace? ¿Qué herramientas recomiendan? ¿Cuáles son los errores?".
- **Input:** Temas detectados por Dipper o selección manual del Jefe en el Command Center.
- **Output:** "Tesoros" (herramientas, links, FAQs) en `agent/research_db.json`.

## 4. William (Editor en Jefe / Redactor)
- **Misión:** Creación del post definitivo (Neo-Brutalista).
- **Rol:** "¿Cómo lo contamos al mundo?".
- **Input:** Señal de Dipper + Sustancia técnica de Perry.
- **Output:** Borradores en `agent/william_drafts/`.

## 5. Sincronización Automática (Auto-Pilot)
- Cuando el modo "AUTO-PILOT" está activo, el Command Center ejecuta:
  Dipper (8:00 AM) -> Perry (Análisis de lo detectado) -> William (Redacción de borrador).
