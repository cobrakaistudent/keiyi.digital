#!/usr/bin/env python3
"""
BENCHMARK — Comparación de modelos Ollama para agentes Keiyi
Ejecuta los mismos prompts en múltiples modelos y mide calidad + velocidad.
Uso: python3 benchmark_models.py
"""
import json
import time
import re
import requests
from datetime import datetime

OLLAMA_API = "http://localhost:11434/api/generate"
RESULTS_FILE = "benchmark_results.json"

# Modelos a comparar
MODELS = ["gemma3:4b", "qwen3:4b"]

# Palabras prohibidas (para validación William)
BANNED = ["reddit", "r/", "subreddit", "ollama", "pipeline", "mac mini",
          "perry", "dipper", "william", "scraping"]

# =============================================================================
# TEST PROMPTS — simulan los prompts reales de Dipper y William
# =============================================================================

TESTS = [
    {
        "name": "Dipper: Extracción Reddit",
        "agent": "dipper",
        "prompt": """Analiza estas discusiones de Reddit de r/MarketingAutomation y extrae inteligencia para una agencia de marketing digital + EdTech con IA en LATAM.

Extrae:
1. Herramientas/software mencionados (nombre + cuántas veces)
2. Pain-points o preguntas recurrentes (texto + frecuencia)
3. URLs de referencia relevantes

REGLAS: Si no hay señal relevante, devuelve listas vacías. Output ONLY valid JSON, sin markdown, sin texto extra.

SCHEMA: {"tools":[{"name":"string","mentions":1}],"questions":[{"text":"string","mentions":1}],"references":["https://..."]}

DATOS:
TÍTULO: Best AI tools for small marketing agencies in 2026
TEXTO: We switched from HubSpot to GoHighLevel last month. The automation features are insane. Also been testing Jasper for ad copy and its much better than ChatGPT for marketing-specific content. Anyone tried Instantly.ai for cold email?

---

TÍTULO: Make.com vs Zapier vs n8n for agency automation
TEXTO: Running a 5 person agency. Need to automate client onboarding, reporting, and social posting. Budget is tight so leaning towards n8n since its open source. Anyone compared these three recently?

---

TÍTULO: Is SEO dead with AI search?
TEXTO: With Google SGE and Perplexity taking over, whats the point of traditional SEO? My organic traffic dropped 30% since January.""",
        "validate": "json",
        "expected_tools": ["GoHighLevel", "Jasper", "Instantly.ai", "Make.com", "Zapier", "n8n"],
    },
    {
        "name": "Dipper: Extracción Hacker News",
        "agent": "dipper",
        "prompt": """Analiza estos artículos/posts de hackernews y extrae inteligencia para una agencia de marketing digital + EdTech con IA en LATAM.

Extrae:
1. Herramientas/plataformas/productos mencionados (nombre + relevancia 1-5)
2. Tendencias o temas recurrentes (descripción + frecuencia)
3. URLs de artículos más relevantes (máx 10)

REGLAS: Si no hay señal relevante, devuelve listas vacías. Output ONLY valid JSON, sin markdown, sin texto extra.

SCHEMA: {"tools":[{"name":"string","mentions":1}],"questions":[{"text":"string","mentions":1}],"references":["https://..."]}

DATOS:
TÍTULO: Show HN: Open source alternative to Notion with AI built-in
URL: https://github.com/example/notionalt
PUNTOS: 342 · COMENTARIOS: 128

TÍTULO: The death of the landing page — why conversational AI is replacing static sites
URL: https://blog.example.com/conversational-ai-landing
PUNTOS: 215 · COMENTARIOS: 89

TÍTULO: Anthropic releases Claude for Enterprise with 1M context
PUNTOS: 890 · COMENTARIOS: 412""",
        "validate": "json",
        "expected_tools": [],
    },
    {
        "name": "William: Blog sobre Make.com",
        "agent": "william",
        "prompt": """Escribe un artículo de blog sobre: "Make.com para automatización de marketing"

Estructura obligatoria:
1. HOOK — 2-3 líneas que enganchen (sin "En el mundo actual...")
2. QUÉ ES — Explica "Make.com" como si el lector NUNCA lo hubiera oído. Usa una analogía simple.
3. QUÉ SIGNIFICA PARA TU NEGOCIO — Por qué debería importarle a un dueño de negocio en LATAM.
4. CÓMO FUNCIONA — Ejemplos concretos y reales.
5. CÓMO EMPEZAR — UNA acción concreta que pueda tomar hoy.
6. CTA — Natural hacia la Academia Keiyi.

REGLAS OBLIGATORIAS:
- NUNCA menciones Reddit, subreddits, foros específicos.
- NUNCA inventes estadísticas.
- NUNCA menciones infraestructura interna de Keiyi (Ollama, agentes, modelos, pipeline, scraping).
- CADA término técnico DEBE tener una explicación breve la PRIMERA vez que aparece.
- El lector es un empresario de LATAM sin background técnico.
- 600-800 palabras. Markdown limpio.""",
        "validate": "blog",
        "expected_tools": [],
    },
    {
        "name": "William: Blog sobre GEO",
        "agent": "william",
        "prompt": """Escribe un artículo de blog sobre: "GEO: Generative Engine Optimization"

Estructura obligatoria:
1. HOOK — 2-3 líneas que enganchen
2. QUÉ ES — Explica GEO como si el lector NUNCA lo hubiera oído. Compáralo con SEO para dar contexto.
3. QUÉ SIGNIFICA PARA TU NEGOCIO — Por qué debería importarle a un dueño de negocio en LATAM.
4. CÓMO FUNCIONA — Pasos concretos.
5. CÓMO EMPEZAR — UNA acción concreta.
6. CTA — Natural hacia la Academia Keiyi.

REGLAS OBLIGATORIAS:
- NUNCA menciones Reddit, subreddits, foros específicos.
- NUNCA inventes estadísticas.
- CADA término técnico DEBE tener una explicación breve.
- El lector es un empresario sin background técnico.
- 600-800 palabras. Markdown limpio.""",
        "validate": "blog",
        "expected_tools": [],
    },
]

# =============================================================================
# SCORING
# =============================================================================

def validate_json(response):
    """Valida respuesta de Dipper: ¿es JSON válido con la estructura esperada?"""
    score = 0
    notes = []

    # Limpiar markdown
    m = re.search(r"```(?:json)?(.*?)```", response, re.DOTALL)
    if m:
        response = m.group(1).strip()
    s = response.find("{")
    e = response.rfind("}")
    if s != -1 and e != -1:
        response = response[s:e+1]

    try:
        data = json.loads(response)
        score += 30
        notes.append("JSON válido")
    except:
        notes.append("JSON INVÁLIDO")
        return 0, notes

    if "tools" in data:
        score += 20
        tools = data["tools"]
        if len(tools) > 0:
            score += 10
            notes.append(f"{len(tools)} herramientas detectadas")
            # Verificar que tienen name y mentions
            if all("name" in t and "mentions" in t for t in tools):
                score += 10
                notes.append("Schema correcto (name+mentions)")
        else:
            notes.append("tools vacío")
    else:
        notes.append("Falta 'tools'")

    if "questions" in data:
        score += 15
        notes.append(f"{len(data['questions'])} preguntas")
    if "references" in data:
        score += 15
        notes.append(f"{len(data['references'])} referencias")

    return score, notes


def validate_blog(response):
    """Valida respuesta de William: calidad editorial."""
    score = 0
    notes = []
    lower = response.lower()
    words = len(response.split())

    # Longitud
    if 400 <= words <= 1000:
        score += 15
        notes.append(f"{words} palabras (OK)")
    else:
        notes.append(f"{words} palabras (fuera de rango)")

    # Tiene título con #
    if re.search(r"^#\s+.+", response, re.MULTILINE):
        score += 10
        notes.append("Tiene título")

    # Tiene secciones con ##
    sections = re.findall(r"^##\s+.+", response, re.MULTILINE)
    if len(sections) >= 3:
        score += 15
        notes.append(f"{len(sections)} secciones")
    else:
        notes.append(f"Solo {len(sections)} secciones")

    # No menciona Reddit
    if "reddit" not in lower and "r/" not in lower:
        score += 15
        notes.append("Sin Reddit")
    else:
        notes.append("MENCIONA REDDIT")

    # No inventa estadísticas
    fake_stats = re.findall(r"el \d{1,3}%\s+de", lower)
    if not fake_stats:
        score += 10
        notes.append("Sin stats inventadas")
    else:
        notes.append("STATS INVENTADAS")

    # No menciona infraestructura
    infra_found = [w for w in BANNED if w in lower]
    if not infra_found:
        score += 10
        notes.append("Sin infra expuesta")
    else:
        notes.append(f"INFRA: {infra_found}")

    # Menciona Keiyi (CTA)
    if "keiyi" in lower:
        score += 10
        notes.append("CTA Keiyi presente")
    else:
        notes.append("Sin CTA Keiyi")

    # Tiene analogía o explicación didáctica
    if any(w in lower for w in ["imagina", "piensa en", "como si", "analogía", "en otras palabras", "es decir"]):
        score += 15
        notes.append("Usa analogías/explicaciones")
    else:
        notes.append("Sin analogías")

    return score, notes


def run_test(model, test):
    """Ejecuta un test en un modelo y retorna resultados."""
    payload = {"model": model, "prompt": test["prompt"], "stream": False}
    if test["validate"] == "json":
        payload["format"] = "json"

    start = time.time()
    try:
        r = requests.post(OLLAMA_API, json=payload, timeout=300)
        elapsed = round(time.time() - start, 2)
        response = r.json().get("response", "")

        if test["validate"] == "json":
            score, notes = validate_json(response)
        else:
            score, notes = validate_blog(response)

        tokens = len(response.split())
        tps = round(tokens / elapsed, 1) if elapsed > 0 else 0

        return {
            "score": score,
            "time_s": elapsed,
            "tokens": tokens,
            "tokens_per_sec": tps,
            "notes": notes,
            "response_preview": response[:300],
        }
    except Exception as e:
        return {
            "score": 0,
            "time_s": round(time.time() - start, 2),
            "tokens": 0,
            "tokens_per_sec": 0,
            "notes": [f"ERROR: {e}"],
            "response_preview": "",
        }


# =============================================================================
# MAIN
# =============================================================================

def main():
    print("=" * 60)
    print("BENCHMARK DE MODELOS OLLAMA — Agentes Keiyi Digital")
    print(f"Fecha: {datetime.now().strftime('%Y-%m-%d %H:%M')}")
    print(f"Modelos: {', '.join(MODELS)}")
    print(f"Tests: {len(TESTS)}")
    print("=" * 60)

    results = {
        "date": datetime.now().isoformat(),
        "models": MODELS,
        "tests": [],
    }

    for test in TESTS:
        print(f"\n{'─' * 50}")
        print(f"TEST: {test['name']}")
        print(f"{'─' * 50}")

        test_result = {"name": test["name"], "agent": test["agent"], "results": {}}

        for model in MODELS:
            print(f"\n  🧠 {model}...", end=" ", flush=True)
            r = run_test(model, test)
            test_result["results"][model] = r
            print(f"Score: {r['score']}/100 · {r['time_s']}s · {r['tokens_per_sec']} tok/s")
            for note in r["notes"]:
                print(f"     {note}")

        results["tests"].append(test_result)

    # Resumen
    print(f"\n{'=' * 60}")
    print("RESUMEN")
    print(f"{'=' * 60}")

    for model in MODELS:
        scores = [t["results"][model]["score"] for t in results["tests"]]
        times = [t["results"][model]["time_s"] for t in results["tests"]]
        avg_score = round(sum(scores) / len(scores), 1)
        avg_time = round(sum(times) / len(times), 1)
        print(f"\n  {model}:")
        print(f"    Score promedio: {avg_score}/100")
        print(f"    Tiempo promedio: {avg_time}s")
        for i, t in enumerate(results["tests"]):
            r = t["results"][model]
            print(f"    {t['name']}: {r['score']}/100 ({r['time_s']}s)")

    # Guardar
    with open(RESULTS_FILE, "w", encoding="utf-8") as f:
        json.dump(results, f, ensure_ascii=False, indent=2)
    print(f"\nResultados guardados en: {RESULTS_FILE}")


if __name__ == "__main__":
    main()
