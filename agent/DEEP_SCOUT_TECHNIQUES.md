# DEEP SCOUT — Registro de Técnicas de Extracción IA

> Propósito: Documentar cada técnica de extracción de inteligencia probada en `deep_scout.py`,
> sus resultados reales y la decisión de qué técnica usar en producción.
>
> Regla: Nunca borrar un experimento. Si una técnica se descarta, se marca [DESCARTADA] con el motivo.

---

## TÉCNICA 1 — Per-Post (Baseline)
**Estado:** BASELINE — referencia de comparación
**Implementada:** 2026-03-05 | **Por:** Claude Code

### Descripción
Una llamada a Ollama (qwen3:8b) por cada post procesado.
Contexto enviado: título + cuerpo + comentarios top, truncado a 2500 chars.

### Código
```python
for post in unique_posts:
    text = f"{title}\n{selftext}\n{comments}"
    extract_tools_ollama(text[:2500], source, research_db)
```

### Pros
- Contexto muy enfocado por post
- Menor riesgo de que Ollama "mezcle" herramientas entre posts
- Fácil de debuggear (sabes de qué post viene cada herramienta)

### Contras
- N posts = N llamadas a Ollama
- Con 50 posts nuevos por subreddit: ~50 llamadas × ~8s c/u = ~7 minutos por subreddit
- No escala bien con múltiples subreddits

### Métricas (llenar con datos reales al ejecutar)
| Métrica | Valor |
|---|---|
| Subreddits probados | — |
| Posts procesados | — |
| Llamadas Ollama | — |
| Tiempo total | — |
| Herramientas únicas encontradas | — |
| Falsos positivos detectados | — |

---

## TÉCNICA 2 — Batch x5 (Activa)
**Estado:** EN PRUEBA — implementada 2026-03-05 | **Por:** Claude Code

### Descripción
Agrupa 5 posts en una sola llamada a Ollama. Pide todas las herramientas de todos los posts
en un solo JSON. Reduce las llamadas en ~80% (50 posts → 10 llamadas).

### Código
```python
BATCH_SIZE = 5
for i in range(0, len(posts_to_analyze), BATCH_SIZE):
    batch = posts_to_analyze[i:i+BATCH_SIZE]
    extract_tools_batch_ollama(batch, source, research_db)
```

### Prompt enviado a Qwen
```
Analiza estos N posts de Reddit e identifica herramientas, software y SaaS mencionados.

POST 1 — [título]: [texto truncado]
POST 2 — [título]: [texto truncado]
...

Responde SOLO con JSON: {"tools": ["tool1", "tool2"]}
```

### Pros
- 5x menos llamadas que T1
- Contexto total manejable (~6000 chars por batch)
- Qwen puede ver patrones entre posts del mismo batch

### Contras
- Riesgo de que Ollama "contamine" herramientas entre posts del batch
- Si un post es muy largo, puede empujar contexto de otros posts fuera del límite
- Menos granularidad en el origen de cada herramienta

### Métricas (llenar con datos reales al ejecutar)
| Métrica | Valor |
|---|---|
| Subreddits probados | — |
| Posts procesados | — |
| Llamadas Ollama | — |
| Tiempo total | — |
| Herramientas únicas encontradas | — |
| Falsos positivos detectados | — |
| Comparación vs T1 (velocidad) | — |
| Comparación vs T1 (calidad) | — |

---

## TÉCNICA 3 — Batch por Subreddit (Planeada)
**Estado:** PLANEADA — pendiente de implementación

### Descripción
Una sola llamada Ollama por subreddit completo. Concatena todos los posts del subreddit,
trunca al límite del contexto y pide extracción global.

### Idea del prompt
```
Analiza estos posts de r/subreddit (semana/mes) e identifica las 20 herramientas
más mencionadas, ordenadas por frecuencia de mención.
Responde: {"tools": [{"name": "HubSpot", "mentions": 12}, ...]}
```

### Cuándo probarla
Después de tener métricas reales de T2. Si T2 da buenos resultados en calidad pero
aún es lento, escalar a T3.

---

## TÉCNICA 4 — Two-Phase (Planeada)
**Estado:** PLANEADA — pendiente de implementación

### Descripción
Fase 1: Regex contra lista curada de ~200 herramientas conocidas (sin Ollama).
Fase 2: Ollama solo para posts que contienen palabras clave pero no matchearon herramientas conocidas.

### Idea
```python
KNOWN_TOOLS = ["hubspot", "mailchimp", "make.com", "zapier", "notion", ...]

def phase1_known_tools(text):
    found = []
    for tool in KNOWN_TOOLS:
        if tool.lower() in text.lower():
            found.append(tool)
    return found

def phase2_unknown(text, phase1_found):
    if len(phase1_found) == 0 and contains_tool_keywords(text):
        return extract_tools_ollama(text)
    return []
```

### Por qué puede ganar
- Para herramientas bien conocidas (HubSpot, Canva, etc.): instantáneo, 0 llamadas Ollama
- Solo usa Ollama para herramientas emergentes/desconocidas
- Potencialmente 10x más rápido que T1, con mejor recall para herramientas nuevas

---

## TABLA COMPARATIVA (actualizar con cada experimento)

| Técnica | Llamadas Ollama (50 posts) | Tiempo est. | Herramientas encontradas | Calidad |
|---|---|---|---|---|
| T1 Per-Post | 50 | ~7 min | — | Referencia |
| T2 Batch x5 | 10 | ~1.5 min | — | Por medir |
| T3 Batch subreddit | 1 | ~15s | — | Por medir |
| T4 Two-Phase | ~5 | ~1 min | — | Por medir |

---

## DECISIÓN ACTUAL
**Técnica activa:** T2 Batch x5
**Motivo:** Primera optimización razonable. Reduce llamadas 5x sin riesgo mayor de calidad.
**Próximo experimento:** T3 una vez que T2 tenga métricas reales.
