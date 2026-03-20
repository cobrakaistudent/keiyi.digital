# GUÍA TÉCNICA: Creación de Agentes Ollama para Keiyi Digital
**Versión:** 1.0 | **Fecha:** 2026-03-10 | **Autor:** Claude Code (investigación + síntesis)
**Estado:** Documento vivo — actualizar conforme se descubran mejores prácticas

---

## Referencias oficiales consultadas

| Fuente | URL | Relevancia |
|---|---|---|
| Ollama Modelfile Docs | https://github.com/ollama/ollama/blob/main/docs/modelfile.md | Referencia primaria — todas las instrucciones |
| Ollama API Reference | https://github.com/ollama/ollama/blob/main/docs/api.md | Parámetros de llamada a la API |
| Ollama Structured Outputs | https://ollama.com/blog/structured-outputs | `format` con schema Pydantic |
| gemma3 model page | https://ollama.com/library/gemma3 | Capacidades y límites del modelo base |
| Ollama Python SDK | https://github.com/ollama/ollama-python | `ollama.chat()` vs `ollama.generate()` |

> **Nota para Perry:** Estas URLs están pendientes de agregar al radar de fuentes para monitoreo continuo.

---

## 1. Instrucciones disponibles en un Modelfile

```
FROM gemma3:4b          # REQUERIDO — modelo base
PARAMETER ...           # parámetros de inferencia (múltiples permitidos)
SYSTEM """..."""        # prompt de sistema — personalidad y formato
TEMPLATE """..."""      # plantilla de chat — NO TOCAR en modelos de librería
MESSAGE user/assistant  # ejemplos few-shot pre-cargados
ADAPTER ./lora.gguf     # LoRA fine-tune (no usamos por ahora)
LICENSE """..."""       # solo para publicar en ollama.com
```

### Regla crítica sobre TEMPLATE
Los modelos de la librería de Ollama (`gemma3:4b`, `llama3.2`, etc.) ya tienen el template correcto integrado. **Nunca agregar TEMPLATE** a menos que se importe un GGUF raw sin template. Sobreescribirlo rompe el instruction-following del modelo.

---

## 2. Todos los PARAMETER disponibles

| Parámetro | Default | Tipo | Descripción |
|---|---|---|---|
| `temperature` | 0.8 | float | Aleatoriedad. 0 = determinístico, 1+ = creativo |
| `top_k` | 40 | int | Tamaño del pool de candidatos por token |
| `top_p` | 0.9 | float | Nucleus sampling — corta el pool por probabilidad acumulada |
| `min_p` | 0.0 | float | Alternativa a top_p — probabilidad mínima relativa |
| `num_ctx` | 2048* | int | Ventana de contexto en tokens |
| `num_predict` | -1 | int | Máximo de tokens a generar. -1 = ilimitado |
| `repeat_penalty` | 1.0 | float | Penaliza secuencias repetidas |
| `repeat_last_n` | 64 | int | Cuántos tokens atrás revisar para repetición. -1 = ctx completo |
| `presence_penalty` | 0.0 | float | Penaliza tokens que ya aparecieron (sin importar frecuencia) |
| `frequency_penalty` | 0.0 | float | Penaliza tokens proporcional a cuántas veces aparecieron |
| `seed` | 0 | int | Semilla fija para output reproducible. 0 = random |
| `stop` | — | string | Secuencia de parada. Múltiples PARAMETER stop permitidos |

*En Apple Silicon con <24 GB VRAM, Ollama auto-asigna 4096 tokens de contexto.

---

## 3. Cómo interactúan temperature, top_k y top_p

Actúan como **filtros secuenciales** en cada paso de generación de tokens:

```
Vocabulario completo (~256k tokens)
    → top_k filtra → queda con los K más probables
    → top_p filtra → queda el subconjunto más pequeño que suma el % de probabilidad
    → temperature reescala las probabilidades del conjunto final
    → se samplea un token
```

**Consecuencia práctica:** Temperature baja con top_k/top_p alto es parcialmente contradictorio. A temperature=0, el argmax siempre gana — el pool de candidatos no importa. Si quieres verdadero determinismo: `temperature 0` + `top_k 1`.

**Combos recomendados para nuestros agentes:**

| Agente | temperature | top_k | top_p | Efecto |
|---|---|---|---|---|
| Dipper (extracción JSON) | 0.0–0.1 | 10 | 0.5 | Casi determinístico |
| Perry (scoring/JSON) | 0.1–0.2 | 20 | 0.65 | JSON consistente |
| William (redacción) | 0.75–0.8 | 50 | 0.92 | Prosa creativa y coherente |
| Agente análisis futuro | 0.3–0.4 | 30 | 0.75 | Razonamiento balanceado |

---

## 4. num_ctx — Cuándo aumentarlo

gemma3:4b soporta hasta 128k tokens de contexto, pero la RAM limita la práctica:

| num_ctx | RAM aprox. (gemma3:4b) | Cuándo usar |
|---|---|---|
| 4096 (auto M2) | ~4.5 GB | Tareas cortas — Perry, William |
| 8192 | ~5.5 GB | Dipper procesando scrapes largos ✅ recomendado |
| 16384 | ~7.5 GB | Análisis de documentos completos |
| 32768 | ~11 GB | Peligroso — deja solo ~5 GB para macOS + apps |

**Regla:** `num_ctx` reserva memoria de KV cache al cargar el modelo. Asignar 32k cuando la tarea necesita 2k desperdicia ~3 GB de RAM que queda bloqueada hasta que Ollama descarga el modelo.

---

## 5. SYSTEM prompt — Mejores prácticas

### Estructura efectiva

1. **Primera línea:** identidad del agente ("Eres Dipper, agente de inteligencia de Keiyi Digital")
2. **MISIÓN:** una oración — qué hace
3. **REGLAS:** lista numerada imperativa — más efectiva que párrafos en prosa
4. **OUTPUT:** formato exacto con ejemplo del schema JSON al **final** del prompt
   - El modelo pesa más las instrucciones del final al iniciar su respuesta

### Reglas de oro

- Para agentes JSON: incluir siempre "Output ONLY valid JSON. No markdown. No prose. No explanation." Sin esto, gemma3:4b frecuentemente envuelve el JSON en triple backticks o agrega "Here is the JSON:"
- Mantener el SYSTEM bajo ~500 tokens para no comerse el num_ctx del contenido real
- **Bug en `perry.modelfile`:** el `###` dentro de las triple-comillas es texto literal que se inyecta en el system prompt — no es una secuencia de parada. La secuencia de parada la define `PARAMETER stop "###"` por separado. Eliminar el `###` de dentro del SYSTEM.

---

## 6. format: "json" vs Pydantic schema vs solo instrucciones

Tres enfoques ordenados por confiabilidad:

### Opción A — Pydantic Schema (MEJOR para extracción)

```python
from ollama import chat
from pydantic import BaseModel

class DipperTrend(BaseModel):
    name: str
    score: int
    summary: str
    primary_source: str

class DipperReport(BaseModel):
    detected_trends: list[DipperTrend]
    recommended_actions: list[dict]
    raw_sources_used: str

response = chat(
    model='keiyi-dipper',
    messages=[{'role': 'user', 'content': scraped_text}],
    format=DipperReport.model_json_schema(),  # constrain token sampling
    options={'temperature': 0},
    stream=False
)
report = DipperReport.model_validate_json(response.message.content)
```

**Por qué es el mejor:** el sampling de tokens queda *constrainado por el schema durante la generación*. El modelo no puede producir JSON inválido — es imposible a nivel de logits. Sin necesidad de regex ni cleanup.

### Opción B — format: "json" (BUENO)

```python
response = ollama.chat(
    model='keiyi-dipper',
    messages=[...],
    format='json',
    options={'temperature': 0},
    stream=False
)
```

Garantiza JSON válido pero no el schema correcto. Aún necesitas el schema en el SYSTEM prompt.

### Opción C — Solo instrucciones en SYSTEM (FRÁGIL)

Lo que hacen los modelfiles actuales. Funciona la mayoría del tiempo con temperature 0.1–0.2, pero gemma3:4b ocasionalmente:
- Envuelve el JSON en markdown code fences
- Agrega texto antes o después del JSON
- Alucina campos extra

**Recomendación:** Dipper y Perry → Opción A. William → Opción B (el campo `content` tiene HTML arbitrario).

---

## 7. Few-shot via MESSAGE — Cuándo y cómo

### Cuándo usar MESSAGE
- El formato de output es inusual y el modelo sigue equivocándose
- Quieres fijar vocabulario/tono específico (el estilo neo-brutalista de William)
- Para mostrar qué hacer con input de baja señal (igual de valioso que el ejemplo positivo)

### Cuándo NO usar MESSAGE
- Para enseñar hechos que el modelo ya sabe
- Para reemplazar un SYSTEM prompt claro
- Ejemplos muy largos — comen num_ctx antes de que llegue el contenido real

### Ejemplo efectivo para Dipper

```
MESSAGE user "AI tool called 'Cursor' wins awards, displaces VS Code for 30% of devs. r/programming top post 9.2k upvotes."
MESSAGE assistant {"detected_trends":[{"name":"Cursor AI adoption","score":88,"summary":"Mainstream IDE displacement accelerating","primary_source":"r/programming","link_referencia":""}],"recommended_actions":[{"title":"Write Cursor vs VS Code comparison","description":"High search intent, strong community signal","priority":"Alta"}],"raw_sources_used":"r/programming 2026-03-10"}

MESSAGE user "Weekend discussion thread. 200 upvotes. Nothing notable."
MESSAGE assistant {"detected_trends":[],"recommended_actions":[],"raw_sources_used":"low-signal weekend content"}
```

El segundo ejemplo (señal baja → listas vacías) es tan importante como el primero.

---

## 8. Errores comunes

| Error | Consecuencia | Fix |
|---|---|---|
| No definir `num_ctx` | Dipper trunca scrapes largos a 2048 tokens | `PARAMETER num_ctx 8192` |
| `###` dentro del SYSTEM (perry.modelfile) | Se inyecta texto basura al system prompt | Remover el `###` de dentro de las triple-comillas |
| Agregar TEMPLATE a modelo de librería | Rompe el instruction-following | Nunca agregar TEMPLATE a gemma3, llama3, etc. |
| `temperature 0.8` en agente de extracción | JSON inconsistente, campos hallucidos | `temperature 0.0` para extracción |
| Sin `num_predict` en agentes de extracción | El modelo puede entrar en loop infinito | `PARAMETER num_predict 1500` |
| Usar `ollama.generate()` en vez de `ollama.chat()` | Bypass del chat template — el modelo no sigue instrucciones correctamente | Siempre `ollama.chat()` para modelos instruct |
| Sin `stream=False` en Python | El pipeline no puede parsear la respuesta hasta que termina de streamear | `stream=False` siempre en pipelines de agentes |

---

## 9. Modelfiles recomendados para nuestros agentes

### keiyi-dipper (NUEVO — por crear)

```modelfile
FROM gemma3:4b

PARAMETER temperature 0.0
PARAMETER top_k 10
PARAMETER top_p 0.5
PARAMETER repeat_penalty 1.1
PARAMETER num_ctx 8192
PARAMETER num_predict 1500
PARAMETER stop "###"

SYSTEM """
Eres Dipper, el Agente de Inteligencia de Keiyi Digital.

MISIÓN: Analizar texto scrapeado de Reddit y extraer tendencias para una agencia de marketing digital + educación con IA en LATAM.

REGLAS:
1. Ignora spam, publicidad y contenido de baja señal.
2. Identifica herramientas, dolores de comunidad y preguntas recurrentes.
3. Clasifica importancia por volumen de menciones (score 0-100).
4. Si no hay señal relevante, devuelve listas vacías — no inventes datos.
5. Output ONLY valid JSON. Sin markdown. Sin texto antes o después del JSON.

OUTPUT:
{
  "detected_trends": [
    { "name": "string", "score": 0-100, "summary": "string", "primary_source": "string", "link_referencia": "string" }
  ],
  "recommended_actions": [
    { "title": "string", "description": "string", "priority": "Alta|Media|Baja" }
  ],
  "raw_sources_used": "string"
}
"""

MESSAGE user "Weekend thread, 150 upvotes, no tools mentioned."
MESSAGE assistant {"detected_trends":[],"recommended_actions":[],"raw_sources_used":"low-signal input"}
```

### keiyi-william (mejoras menores al actual)

Agregar al `william.modelfile` existente:
```
PARAMETER repeat_penalty 1.15
PARAMETER num_predict 2048
PARAMETER num_ctx 4096
```

### perry (fixes al modelfile existente)

- Cambiar `FROM llama3.2:1b` → `FROM gemma3:4b` (llama3.2:1b no está instalado)
- Ajustar `top_p` de 0.85 → 0.65 (más coherente con temperature 0.15)
- Apretar `top_k` de 30 → 20
- Remover el `###` de dentro del bloque SYSTEM
- Agregar `PARAMETER num_predict 512`

---

## 10. Comandos para crear/actualizar modelos

```bash
# Crear Dipper desde cero
ollama create keiyi-dipper -f agent/dipper.modelfile

# Reconstruir William con mejoras
ollama create keiyi-william -f agent/william.modelfile

# Verificar que el modelo cargó correctamente
ollama show keiyi-dipper

# Probar con prompt rápido
ollama run keiyi-dipper "Test: r/marketing thread about AI tools."

# Ver todos los modelos
ollama list
```

---

## 11. Próximos pasos para el equipo

| Tarea | Responsable | Prioridad |
|---|---|---|
| Crear `agent/dipper.modelfile` con el template de esta guía | Gemini CLI | 🔴 Alta |
| Ejecutar `ollama create keiyi-dipper` | Gemini CLI | 🔴 Alta |
| Actualizar `idle_config.json`: dipper backend → `"ollama"`, modelo → `"keiyi-dipper"` | Claude Code | 🔴 Alta |
| Actualizar `idle_config.json`: william backend → `"ollama"`, modelo → `"keiyi-william"` | Claude Code | 🔴 Alta |
| Arreglar `perry.modelfile`: `FROM`, `top_p`, `top_k`, remover `###` del SYSTEM | Claude Code | 🟡 Media |
| Agregar `repeat_penalty` y `num_predict` a `william.modelfile` | Claude Code | 🟡 Media |
| Migrar llamadas de Dipper a Pydantic schema (Opción A) en `deep_scout.py` | Claude Code | 🟡 Media |
| Agregar referencias de Ollama docs al radar de Perry para monitoreo | Claude Code | 🟢 Baja |

---

*Este documento se actualiza conforme se validen las configuraciones en producción.*
