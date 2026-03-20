#!/usr/bin/env python3
"""
WILLIAM — Editor Investigador de Keiyi Digital
Pipeline: seleccionar tema → investigar URLs → redactar → guardar borrador
"""
import os
import re
import sys
import json
import requests
from bs4 import BeautifulSoup
from datetime import datetime

# ==============================================================================
# CONFIG
# ==============================================================================

OLLAMA_API_URL = "http://localhost:11434/api/generate"
MODEL = "keiyi-william"
GDRIVE_INTEL = os.path.expanduser(
    "~/Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/keiyi_scout_intelligence"
)
RESEARCH_DB = os.path.join(GDRIVE_INTEL, "research_db.json")
WEEKLY_BRIEF = os.path.join(GDRIVE_INTEL, "weekly_brief.json")
DRAFTS_DIR = os.path.expanduser(
    "~/Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/william_drafts"
)
HISTORY_FILE = os.path.join(DRAFTS_DIR, "william_history.json")

os.makedirs(DRAFTS_DIR, exist_ok=True)

# Palabras prohibidas — si aparecen en el output, el draft se rechaza
BANNED_WORDS = [
    "reddit", "subreddit", "r/", "/r/",
    "ollama", "pipeline", "agente de ia", "mac mini",
    "perry", "dipper", "william",
    "scraping", "scrapeando", "rastreando tendencias",
]

# ==============================================================================
# DATA LOADING
# ==============================================================================

def load_research_db():
    if not os.path.exists(RESEARCH_DB):
        return None
    with open(RESEARCH_DB, 'r', encoding='utf-8') as f:
        return json.load(f)

def load_history():
    if not os.path.exists(HISTORY_FILE):
        return {"drafted_topics": [], "last_run": None}
    with open(HISTORY_FILE, 'r', encoding='utf-8') as f:
        return json.load(f)

def save_history(history):
    with open(HISTORY_FILE, 'w', encoding='utf-8') as f:
        json.dump(history, f, ensure_ascii=False, indent=2)

def load_weekly_brief():
    """Carga el brief editorial de Dipper (ángulos sugeridos para artículos)."""
    if not os.path.exists(WEEKLY_BRIEF):
        return None
    try:
        with open(WEEKLY_BRIEF, 'r', encoding='utf-8') as f:
            return json.load(f)
    except:
        return None

def validate_draft(content):
    """Revisa que el draft no contenga palabras prohibidas. Retorna (ok, problemas)."""
    problems = []
    lower = content.lower()
    for word in BANNED_WORDS:
        if word.lower() in lower:
            problems.append(f'Contiene "{word}"')
    # Detectar estadísticas inventadas (patrón: "el X% de")
    fake_stats = re.findall(r'el \d{1,3}%\s+de', lower)
    if fake_stats:
        problems.append(f'Posible estadística inventada: {fake_stats[0]}')
    return len(problems) == 0, problems

# ==============================================================================
# TOPIC SELECTION — Pick the best undrafted topics
# ==============================================================================

def select_topics(db, history, count=3):
    """
    Selecciona temas para redactar, priorizando:
    1. Ángulos editoriales del weekly_brief de Dipper (si existen)
    2. Herramientas con más menciones cross-fuente
    3. Preguntas recurrentes de la comunidad
    Mezcla: al menos 1 de cada tipo disponible para diversidad.
    """
    drafted = set(t.lower() for t in history.get("drafted_topics", []))

    # === FUENTE 1: Ángulos editoriales de Dipper ===
    brief = load_weekly_brief()
    angle_topics = []
    if brief and brief.get("editorial_angles"):
        for angle in brief["editorial_angles"]:
            title = angle.get("title", "").strip()
            if not title or title.lower() in drafted:
                continue
            angle_topics.append({
                "name": title,
                "count": 99,  # prioridad alta
                "sources": [angle.get("main_topic", "editorial")],
                "refs": [],
                "type": "editorial",
                "hook": angle.get("hook", ""),
                "target_audience": angle.get("target_audience", ""),
                "urgency": angle.get("urgency", "media"),
            })
        if angle_topics:
            print(f"  📋 {len(angle_topics)} ángulos editoriales de Dipper disponibles")

    # === FUENTE 2: Herramientas con más menciones ===
    tool_scores = {}
    for sub_name, sub_data in db.items():
        if not isinstance(sub_data, dict):
            continue
        for t in sub_data.get("tools", []):
            name = t.get("name", "").strip()
            if not name or name.lower() in drafted:
                continue
            key = name.lower()
            if key not in tool_scores:
                tool_scores[key] = {"name": name, "count": 0, "sources": [], "refs": []}
            tool_scores[key]["count"] += t.get("count", 1)
            tool_scores[key]["sources"].append(sub_name)

        for r in sub_data.get("references", []):
            url = r.get("url") or r.get("name", "")
            if url and "reddit.com" not in url:
                for t in sub_data.get("tools", []):
                    tkey = t.get("name", "").lower()
                    if tkey in tool_scores:
                        tool_scores[tkey]["refs"].append(url)

    ranked_tools = sorted(tool_scores.values(), key=lambda x: -x["count"])
    for t in ranked_tools:
        t["type"] = "tool"

    # === FUENTE 3: Preguntas recurrentes ===
    question_topics = []
    for sub_name, sub_data in db.items():
        if not isinstance(sub_data, dict):
            continue
        for q in sub_data.get("questions", []):
            text = q.get("text", "").strip()
            if not text or len(text) < 20:
                continue
            qcount = q.get("count", 1)
            if qcount >= 2 and text.lower() not in drafted:
                question_topics.append({
                    "name": text[:80],
                    "count": qcount,
                    "sources": [sub_name],
                    "refs": [],
                    "type": "question"
                })
    question_topics.sort(key=lambda x: -x["count"])

    # === MEZCLA DIVERSA ===
    # Prioridad: 1 editorial + 1 tool + 1 question (si hay de cada tipo)
    topics = []
    a_idx, t_idx, q_idx = 0, 0, 0
    sources = [
        ("editorial", angle_topics),
        ("tool", ranked_tools),
        ("question", question_topics),
    ]

    # Primera pasada: 1 de cada tipo para garantizar diversidad
    for label, pool in sources:
        if len(topics) >= count:
            break
        idx = 0
        while idx < len(pool) and len(topics) >= count:
            break
        if idx < len(pool):
            topics.append(pool[idx])

    # Segunda pasada: llenar resto por prioridad (editorial > tool > question)
    all_remaining = []
    for label, pool in sources:
        used_names = {t["name"].lower() for t in topics}
        for item in pool:
            if item["name"].lower() not in used_names:
                all_remaining.append(item)

    all_remaining.sort(key=lambda x: -x["count"])
    for item in all_remaining:
        if len(topics) >= count:
            break
        topics.append(item)

    return topics

# ==============================================================================
# INVESTIGATION — Scrape reference URLs for context
# ==============================================================================

def investigate(urls):
    """Fetch and extract text from reference URLs."""
    if not urls:
        return ""
    print(f"  🕵️ Investigando {len(urls)} fuentes...")
    context = ""
    for url in urls[:2]:  # max 2 per topic
        try:
            r = requests.get(url, timeout=10, headers={"User-Agent": "Mozilla/5.0"})
            soup = BeautifulSoup(r.text, 'html.parser')
            for tag in soup(['script', 'style', 'nav', 'footer', 'header']):
                tag.decompose()
            text = soup.get_text(separator=" ", strip=True)[:2000]
            context += f"\n--- {url} ---\n{text}\n"
        except Exception as e:
            print(f"  ⚠️ No pude leer {url}: {e}")
    return context

# ==============================================================================
# REDACTION — Generate blog post via Ollama
# ==============================================================================

def redact_topic(topic, deep_context=""):
    """Generate a blog post for a specific topic. Retries once if validation fails."""
    topic_name = topic["name"]
    topic_type = topic.get("type", "tool")
    mentions = topic["count"]

    # === Construir ángulo según tipo de tema ===
    if topic_type == "editorial":
        # Tema viene del weekly_brief de Dipper con hook y audiencia
        hook = topic.get("hook", "")
        audience = topic.get("target_audience", "empresarios de LATAM")
        angle = f"""Escribe un artículo de blog con este ángulo editorial:
TÍTULO SUGERIDO: "{topic_name}"
HOOK: {hook}
AUDIENCIA: {audience}

Estructura obligatoria:
1. HOOK — Usa el hook sugerido o mejóralo. 2-3 líneas que enganchen.
2. QUÉ ES — Explica el tema central como si el lector NUNCA lo hubiera oído. Usa una analogía cotidiana.
3. QUÉ SIGNIFICA PARA TU NEGOCIO — Impacto concreto para un dueño de negocio en LATAM.
4. CÓMO FUNCIONA — Ejemplos reales y concretos.
5. CÓMO EMPEZAR — UNA acción concreta que pueda tomar hoy.
6. CTA — Natural hacia la Academia Keiyi."""

    elif topic_type == "question":
        angle = f"""Escribe un artículo de blog que responda esta pregunta frecuente entre profesionales del sector:
"{topic_name}"

Responde con autoridad. Primero explica QUÉ ES el concepto (como si el lector nunca lo hubiera oído), luego QUÉ SIGNIFICA para un negocio en LATAM, y después da pasos concretos para actuar."""

    else:  # tool
        angle = f"""Escribe un artículo de blog sobre: "{topic_name}"

Este tema está ganando relevancia entre profesionales de tecnología y marketing.
Estructura obligatoria:
1. HOOK — 2-3 líneas que enganchen (sin "En el mundo actual...")
2. QUÉ ES — Explica "{topic_name}" como si el lector NUNCA lo hubiera oído. Usa una analogía simple.
3. QUÉ SIGNIFICA PARA TU NEGOCIO — Por qué debería importarle a un dueño de negocio en LATAM.
4. CÓMO FUNCIONA — Ejemplos concretos y reales.
5. CÓMO EMPEZAR — UNA acción concreta que pueda tomar hoy.
6. CTA — Natural hacia la Academia Keiyi."""

    prompt = f"""{angle}

{f'Contexto de investigación (usa como referencia, NO copies textual):{chr(10)}{deep_context[:2000]}' if deep_context else ''}

REGLAS OBLIGATORIAS:
- NUNCA menciones Reddit, subreddits, foros específicos, ni "la comunidad de Reddit".
- NUNCA inventes estadísticas. NO escribas porcentajes ni cifras que no puedas respaldar con una fuente pública (Gartner, Statista, Google).
- NUNCA menciones infraestructura interna de Keiyi (Ollama, agentes, modelos, pipeline, scraping).
- Habla de Keiyi como empresa profesional establecida.
- CADA término técnico que uses (ROAS, CRM, retargeting, lead, funnel, CAC, engagement, etc.) DEBE tener una explicación breve entre paréntesis o en la siguiente oración la PRIMERA vez que aparece. Usa analogías cotidianas.
- El lector es un empresario de LATAM sin background técnico. Escribe para él.
- UNA sola idea fuerte por artículo. No cubras 5 temas.
- Párrafos cortos (máximo 4 líneas).
- Título ESPECÍFICO — no genérico ("El Marketing Está Muerto" está PROHIBIDO). Incluye el nombre de la herramienta o concepto.
- 600-800 palabras.

FORMATO: Escribe en Markdown limpio. Usa # para título, ## para secciones, **negritas**, listas con -. NO uses HTML. NO uses JSON."""

    # === Generar con retry si falla validación ===
    for attempt in range(2):
        payload = {"model": MODEL, "prompt": prompt, "stream": False}
        try:
            label = f"(intento {attempt + 1})" if attempt > 0 else ""
            print(f"  ✍️ Redactando sobre: {topic_name} {label}...")
            r = requests.post(OLLAMA_API_URL, json=payload, timeout=300)
            response = r.json().get("response", "")

            if len(response.strip()) < 200:
                print(f"  ⚠️ Respuesta muy corta ({len(response)} chars), descartando")
                return None

            # === VALIDACIÓN DE CALIDAD ===
            ok, problems = validate_draft(response)
            if not ok:
                print(f"  🚫 Draft rechazado: {', '.join(problems)}")
                if attempt == 0:
                    # Agregar advertencia extra al prompt para el retry
                    prompt += "\n\nADVERTENCIA: Tu respuesta anterior fue RECHAZADA porque: " + "; ".join(problems) + ". Corrígelo."
                    continue
                else:
                    print(f"  ⚠️ Segundo intento también falló, guardando con advertencia")

            post = {
                "content": response.strip(),
                "source_topic": topic_name,
                "topic_type": topic_type,
                "source_mentions": mentions,
                "validation": "PASS" if ok else f"WARN: {', '.join(problems)}",
                "generated_at": datetime.now().isoformat()
            }
            return post
        except Exception as e:
            print(f"  ❌ Error al redactar: {e}")
            return None

    return None

# ==============================================================================
# MAIN
# ==============================================================================

def main():
    count = 3  # default
    if len(sys.argv) > 1:
        try:
            count = int(sys.argv[1])
        except ValueError:
            pass

    print(f"🖋️ William · Iniciando sesión de redacción ({count} borradores)")
    print(f"   Modelo: {MODEL}")
    print(f"   Destino: {DRAFTS_DIR}")
    print()

    db = load_research_data()
    if not db:
        print("📭 Sin research_db.json — ejecuta Dipper primero")
        return

    history = load_history()
    topics = select_topics(db, history, count=count)

    if not topics:
        print("📭 No hay temas nuevos para redactar (todos ya fueron cubiertos)")
        return

    print(f"📋 Temas seleccionados:")
    type_icons = {"editorial": "📋", "tool": "🔧", "question": "❓"}
    for i, t in enumerate(topics):
        icon = type_icons.get(t.get('type', 'tool'), '📄')
        print(f"   {i+1}. {icon} [{t.get('type','tool').upper()}] {t['name']}")
        if t.get('type') == 'editorial' and t.get('hook'):
            print(f"      Hook: {t['hook'][:80]}...")
    print()

    drafted = 0
    for topic in topics:
        # Investigate reference URLs
        refs = list(set(topic.get("refs", [])))
        deep_ctx = investigate(refs) if refs else ""

        # Redact
        post = redact_topic(topic, deep_ctx)
        if not post:
            continue

        # Save draft as Markdown
        ts = datetime.now().strftime('%Y%m%d_%H%M%S')
        filename = f"draft_{ts}_{drafted + 1}.md"
        filepath = os.path.join(DRAFTS_DIR, filename)

        # Write Markdown with metadata header
        validation = post.get("validation", "PASS")
        topic_type = post.get("topic_type", "tool")
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(f"<!-- William Draft | Tipo: {topic_type} | Tema: {topic['name']} | Validación: {validation} | Fecha: {datetime.now().isoformat()} -->\n\n")
            f.write(post["content"])

        print(f"  ✅ Borrador guardado: {filename}")
        drafted += 1

        # Track in history
        history["drafted_topics"].append(topic["name"])

    # Save history
    history["last_run"] = datetime.now().isoformat()
    history["total_drafts"] = history.get("total_drafts", 0) + drafted
    save_history(history)

    print(f"\n🏁 William terminó: {drafted}/{len(topics)} borradores generados")

def load_research_data():
    return load_research_db()

if __name__ == "__main__":
    main()
