#!/usr/bin/env python3
"""
DIPPER — El Detective de Inteligencia de Keiyi Digital
=======================================================
Excava múltiples tipos de fuentes y extrae inteligencia accionable.

Fuentes soportadas:
  - Reddit       (r/subreddit)         → API JSON oficial
  - Hacker News  (news.ycombinator)    → Firebase API
  - RSS / Blogs  (feeds)               → feedparser
  - GitHub       (github.com/*)        → GitHub REST API
  - Web genérico (cualquier URL)       → RSS primero, HTML fallback

Uso:
  python3 dipper_scout.py                   → RADAR (todas las fuentes de Perry)
  python3 dipper_scout.py r/SaaS            → excava un subreddit específico
  python3 dipper_scout.py https://...       → excava una URL específica
"""

import os, sys, json, hashlib, re, requests, subprocess, threading
from datetime import datetime
from urllib.parse import urlparse

# ==============================================================================
# CONFIG
# ==============================================================================

OLLAMA_API_URL = "http://localhost:11434/api/generate"
MODEL          = "keiyi-dipper"
GEMINI_BIN     = "/opt/homebrew/bin/gemini"
AGENT_DIR      = os.path.dirname(os.path.abspath(__file__))
CONFIG_FILE    = os.path.join(AGENT_DIR, "dipper_config.json")

GDRIVE_INTEL = os.path.expanduser(
    "~/Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com"
    "/My Drive/gemini/keiyi_scout_intelligence"
)
os.makedirs(GDRIVE_INTEL, exist_ok=True)

RESEARCH_DB  = os.path.join(GDRIVE_INTEL, "research_db.json")
SEEN_FILE    = os.path.join(GDRIVE_INTEL, "seen_comments.json")
HOT_FILE     = os.path.join(GDRIVE_INTEL, "hot_sources.json")
WEEKLY_BRIEF = os.path.join(GDRIVE_INTEL, "weekly_brief.json")

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
                  "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
}

POSTS_PER_SUB  = 15
HN_ITEMS_LIMIT = 30
RSS_ITEMS_LIMIT = 20
GH_REPOS_LIMIT = 10
MAX_RADAR      = 12   # fuentes por run en modo RADAR

# Fuentes por defecto si Perry no tiene hot_sources aún
DEFAULT_RADAR = [
    "r/LocalLLaMA", "r/SaaS", "r/AI_Agents",
    "r/Entrepreneur", "r/MarketingAutomation",
    "https://news.ycombinator.com/",
]

def load_backend() -> str:
    try:
        with open(CONFIG_FILE) as f:
            return json.load(f).get("backend", "ollama")
    except:
        return "ollama"

BACKEND = load_backend()

# ==============================================================================
# UTILIDADES
# ==============================================================================

def load_seen() -> set:
    if not os.path.exists(SEEN_FILE):
        return set()
    with open(SEEN_FILE) as f:
        return set(json.load(f))

def save_seen(seen: set):
    with open(SEEN_FILE, "w") as f:
        json.dump(list(seen), f)

def hash_text(text: str) -> str:
    return hashlib.md5(text.strip().encode()).hexdigest()

def source_key(url: str) -> str:
    """Genera clave única para usar como key en research_db."""
    if url.startswith("r/"):
        return url
    parsed = urlparse(url if "://" in url else f"https://{url}")
    host   = parsed.netloc.replace("www.", "")
    path   = parsed.path.strip("/").split("/")[0] if parsed.path.strip("/") else ""
    if "ycombinator.com" in host:
        return "hackernews"
    if "github.com" in host:
        return f"github/{path}" if path else "github"
    return host or url

# ==============================================================================
# DETECCIÓN DE TIPO DE FUENTE
# ==============================================================================

def detect_source_type(url: str) -> str:
    """Clasifica una URL: 'reddit' | 'hackernews' | 'github' | 'web'"""
    u = url.lower()
    if u.startswith("r/") or "reddit.com/r/" in u:
        return "reddit"
    if "ycombinator.com" in u or "hacker-news.firebaseio" in u:
        return "hackernews"
    if "github.com" in u:
        return "github"
    return "web"   # RSS primero, HTML fallback

# ==============================================================================
# SCRAPING — Reddit
# ==============================================================================

def scrape_reddit(sub: str, seen: set, limit: int = POSTS_PER_SUB) -> tuple[str, set]:
    texts, new_hashes = [], set()
    for sort, params in [("hot", f"limit={limit}"), ("top", f"limit={limit}&t=week")]:
        try:
            r = requests.get(
                f"https://www.reddit.com/r/{sub}/{sort}.json?{params}",
                headers=HEADERS, timeout=15
            )
            if r.status_code != 200:
                print(f"  ⚠️  r/{sub}/{sort} → HTTP {r.status_code}"); continue
            for p in r.json()["data"]["children"]:
                d = p["data"]
                if d.get("removed_by_category") or d.get("selftext") == "[removed]":
                    continue
                text = f"TÍTULO: {d['title']}\n{d.get('selftext','')[:600]}"
                h = hash_text(text)
                if h not in seen:
                    new_hashes.add(h)
                    texts.append(text)
        except Exception as e:
            print(f"  ⚠️  Error r/{sub}/{sort}: {e}")
    return "\n\n---\n\n".join(texts), new_hashes

# ==============================================================================
# SCRAPING — Hacker News
# ==============================================================================

HN_BASE = "https://hacker-news.firebaseio.com/v0"

def scrape_hackernews(seen: set, limit: int = HN_ITEMS_LIMIT) -> tuple[str, set]:
    """Top stories via Firebase API (JSON limpio, sin HTML)."""
    texts, new_hashes = [], set()
    try:
        ids = requests.get(f"{HN_BASE}/topstories.json", timeout=10).json()[:limit * 2]
        count = 0
        for item_id in ids:
            if count >= limit:
                break
            try:
                item = requests.get(f"{HN_BASE}/item/{item_id}.json", timeout=8).json()
                if not item or item.get("dead") or item.get("deleted"):
                    continue
                title  = item.get("title", "")
                body   = item.get("text", "")       # Ask HN tiene texto
                url    = item.get("url", "")
                score  = item.get("score", 0)
                cmts   = item.get("descendants", 0)
                parts  = [f"TÍTULO: {title}"]
                if url:  parts.append(f"URL: {url}")
                if body: parts.append(f"TEXTO: {re.sub(r'<[^>]+>',' ',body)[:400]}")
                parts.append(f"PUNTOS: {score} · COMENTARIOS: {cmts}")
                entry = "\n".join(parts)
                h = hash_text(entry)
                if h not in seen:
                    new_hashes.add(h); texts.append(entry); count += 1
            except:
                pass
    except Exception as e:
        print(f"  ⚠️  Error HN: {e}")
    print(f"  📰 {len(texts)} stories de Hacker News")
    return "\n\n---\n\n".join(texts), new_hashes

# ==============================================================================
# SCRAPING — RSS / Blogs / Newsletters
# ==============================================================================

RSS_VARIANTS = [
    "/feed", "/rss", "/feed.xml", "/rss.xml", "/atom.xml",
    "/blog/rss", "/blog/feed", "/blog/rss.xml", "/blog/feed.xml",
    "/news/feed", "/articles/feed",
]

def scrape_rss(url: str, seen: set, limit: int = RSS_ITEMS_LIMIT) -> tuple[str, set]:
    """Intenta leer el feed RSS. Prueba variantes comunes si la URL base no es feed."""
    try:
        import feedparser
    except ImportError:
        print("  ⚠️  feedparser no instalado — pip install feedparser")
        return "", set()

    base = url.rstrip("/")
    feed = None
    for attempt in [base] + [base + s for s in RSS_VARIANTS]:
        try:
            parsed = feedparser.parse(attempt)
            if parsed.entries:
                feed = parsed
                print(f"  📡 RSS: {attempt}")
                break
        except:
            pass

    if not feed or not feed.entries:
        return "", set()

    texts, new_hashes = [], set()
    for entry in feed.entries[:limit]:
        title   = entry.get("title", "")
        summary = entry.get("summary", "")
        if not summary and entry.get("content"):
            summary = entry["content"][0].get("value", "")
        summary = re.sub(r"<[^>]+>", " ", summary)[:500].strip()
        link    = entry.get("link", "")
        entry_text = f"TÍTULO: {title}\nRESUMEN: {summary}\nURL: {link}"
        h = hash_text(entry_text)
        if h not in seen:
            new_hashes.add(h); texts.append(entry_text)

    print(f"  📰 {len(texts)} artículos nuevos en feed")
    return "\n\n---\n\n".join(texts), new_hashes

# ==============================================================================
# SCRAPING — GitHub
# ==============================================================================

def scrape_github(url: str, seen: set, limit: int = GH_REPOS_LIMIT) -> tuple[str, set]:
    """Repos recientes de un usuario/org o info de un repo específico."""
    parsed = urlparse(url if "://" in url else f"https://{url}")
    parts  = [p for p in parsed.path.strip("/").split("/") if p]
    if not parts:
        return "", set()

    texts, new_hashes = [], set()
    try:
        if len(parts) >= 2:
            # Repo específico
            r = requests.get(
                f"https://api.github.com/repos/{parts[0]}/{parts[1]}",
                headers=HEADERS, timeout=10
            )
            if r.status_code == 200:
                repo  = r.json()
                entry = (
                    f"REPO: {repo['full_name']}\n"
                    f"DESCRIPCIÓN: {repo.get('description','')}\n"
                    f"ESTRELLAS: {repo.get('stargazers_count',0)}\n"
                    f"TEMAS: {', '.join(repo.get('topics',[]))}\n"
                    f"URL: {repo.get('html_url','')}"
                )
                h = hash_text(entry)
                if h not in seen:
                    new_hashes.add(h); texts.append(entry)
        else:
            # Usuario u organización
            r = requests.get(
                f"https://api.github.com/users/{parts[0]}/repos?sort=updated&per_page={limit}",
                headers=HEADERS, timeout=10
            )
            if r.status_code == 200:
                for repo in r.json():
                    if repo.get("fork"):
                        continue
                    entry = (
                        f"REPO: {repo['full_name']}\n"
                        f"DESCRIPCIÓN: {repo.get('description','')}\n"
                        f"ESTRELLAS: {repo.get('stargazers_count',0)}\n"
                        f"TEMAS: {', '.join(repo.get('topics',[]))}\n"
                        f"URL: {repo.get('html_url','')}"
                    )
                    h = hash_text(entry)
                    if h not in seen:
                        new_hashes.add(h); texts.append(entry)
    except Exception as e:
        print(f"  ⚠️  Error GitHub {url}: {e}")

    print(f"  🐙 {len(texts)} repos de GitHub")
    return "\n\n---\n\n".join(texts), new_hashes

# ==============================================================================
# SCRAPING — Web genérico (RSS primero, HTML fallback)
# ==============================================================================

def scrape_web(url: str, seen: set) -> tuple[str, set]:
    # Intentar RSS primero
    rss_text, rss_hashes = scrape_rss(url, seen)
    if rss_text:
        return rss_text, rss_hashes

    # Fallback: HTML con BeautifulSoup
    try:
        from bs4 import BeautifulSoup
        r = requests.get(url, headers=HEADERS, timeout=15)
        if r.status_code != 200:
            print(f"  ⚠️  HTTP {r.status_code} para {url}")
            return "", set()
        soup   = BeautifulSoup(r.text, "html.parser")
        texts, new_hashes = [], set()
        for tag in soup.find_all(["h1","h2","h3","h4","p","li"])[:50]:
            text = tag.get_text(separator=" ", strip=True)
            if len(text) < 30:
                continue
            h = hash_text(text)
            if h not in seen:
                new_hashes.add(h); texts.append(text)
        print(f"  🌐 {len(texts)} fragmentos HTML de {url}")
        return "\n\n".join(texts[:30]), new_hashes
    except Exception as e:
        print(f"  ⚠️  Error web {url}: {e}")
        return "", set()

# ==============================================================================
# DISPATCHER DE SCRAPING
# ==============================================================================

def scrape_source(url: str, seen: set) -> tuple[str, set, str]:
    """Detecta tipo y llama al scraper correcto. Retorna (texto, hashes, tipo)."""
    stype = detect_source_type(url)

    if stype == "reddit":
        sub = url[2:] if url.startswith("r/") else url.rstrip("/").split("/r/")[-1]
        text, hashes = scrape_reddit(sub, seen)
    elif stype == "hackernews":
        text, hashes = scrape_hackernews(seen)
    elif stype == "github":
        text, hashes = scrape_github(url, seen)
    else:
        text, hashes = scrape_web(url, seen)

    return text, hashes, stype

# ==============================================================================
# PROMPTS — uno por tipo de fuente
# ==============================================================================

REDDIT_PROMPT = (
    "Analiza estas discusiones de Reddit de r/{source} y extrae inteligencia para "
    "una agencia de marketing digital + EdTech con IA en LATAM.\n\n"
    "Extrae:\n"
    "1. Herramientas/software mencionados (nombre + cuántas veces)\n"
    "2. Pain-points o preguntas recurrentes (texto + frecuencia)\n"
    "3. URLs de referencia relevantes\n\n"
    "REGLAS: Si no hay señal relevante, devuelve listas vacías. "
    "Output ONLY valid JSON, sin markdown, sin texto extra.\n\n"
    'SCHEMA: {{"tools":[{{"name":"string","mentions":1}}],'
    '"questions":[{{"text":"string","mentions":1}}],"references":["https://..."]}}\n\n'
    "DATOS:\n{text}"
)

ARTICLE_PROMPT = (
    "Analiza estos artículos/posts de {source} y extrae inteligencia para "
    "una agencia de marketing digital + EdTech con IA en LATAM.\n\n"
    "Extrae:\n"
    "1. Herramientas/plataformas/productos mencionados (nombre + relevancia 1-5)\n"
    "2. Tendencias o temas recurrentes (descripción + frecuencia)\n"
    "3. URLs de artículos más relevantes (máx 10)\n\n"
    "REGLAS: Si no hay señal relevante, devuelve listas vacías. "
    "Output ONLY valid JSON, sin markdown, sin texto extra.\n\n"
    'SCHEMA: {{"tools":[{{"name":"string","mentions":1}}],'
    '"questions":[{{"text":"string","mentions":1}}],"references":["https://..."]}}\n\n'
    "DATOS:\n{text}"
)

GITHUB_PROMPT = (
    "Analiza estos repositorios de GitHub de {source} y extrae inteligencia técnica para "
    "una agencia de marketing digital + EdTech con IA en LATAM.\n\n"
    "Extrae:\n"
    "1. Herramientas/librerías/frameworks (nombre + estrellas como relevancia)\n"
    "2. Casos de uso o problemas que resuelven (descripción + frecuencia)\n"
    "3. URLs de los repos más relevantes\n\n"
    "REGLAS: Si no hay señal relevante, devuelve listas vacías. "
    "Output ONLY valid JSON, sin markdown, sin texto extra.\n\n"
    'SCHEMA: {{"tools":[{{"name":"string","mentions":1}}],'
    '"questions":[{{"text":"string","mentions":1}}],"references":["https://..."]}}\n\n'
    "REPOS:\n{text}"
)

SYNTHESIS_PROMPT = (
    "Eres el árbitro de inteligencia de Keiyi Digital.\n\n"
    "Dos agentes analizaron el mismo contenido de {source}:\n\n"
    "--- ANÁLISIS OLLAMA ---\n{ollama}\n\n"
    "--- ANÁLISIS GEMINI ---\n{gemini}\n\n"
    "Produce un JSON unificado que combine lo mejor de ambos:\n"
    "- Suma mentions cuando ambos coinciden en la misma herramienta\n"
    "- Incluye preguntas únicas de ambos sin duplicados\n"
    "- Prioriza referencias más relevantes (máx 10)\n\n"
    "Output ONLY valid JSON:\n"
    '{{"tools":[{{"name":"string","mentions":1}}],'
    '"questions":[{{"text":"string","mentions":1}}],"references":["https://..."]}}'
)

def build_prompt(source_type: str, source: str, text: str) -> str:
    if source_type == "reddit":
        return REDDIT_PROMPT.format(source=source, text=text[:4000])
    if source_type == "github":
        return GITHUB_PROMPT.format(source=source, text=text[:4000])
    return ARTICLE_PROMPT.format(source=source, text=text[:4000])  # HN, web, RSS

# ==============================================================================
# EXTRACCIÓN — helpers compartidos
# ==============================================================================

def _parse_json(raw: str) -> dict | None:
    m = re.search(r"```(?:json)?(.*?)```", raw, re.DOTALL)
    if m: raw = m.group(1).strip()
    s = raw.find("{"); e = raw.rfind("}")
    if s != -1 and e != -1: raw = raw[s:e+1]
    try:
        result = json.loads(raw)
        return result if isinstance(result, dict) else None
    except json.JSONDecodeError as ex:
        print(f"  ⚠️  JSON inválido: {ex}")
        return None

def _run_ollama(prompt: str) -> dict | None:
    payload = {"model": MODEL, "prompt": prompt, "format": "json", "stream": False}
    try:
        r = requests.post(OLLAMA_API_URL, json=payload, timeout=300)
        return _parse_json(r.json().get("response", "{}"))
    except Exception as e:
        print(f"  ❌ Ollama error: {e}"); return None

def _run_gemini(prompt: str) -> dict | None:
    try:
        r = subprocess.run([GEMINI_BIN, "-p", prompt],
                           capture_output=True, text=True, timeout=180)
        return _parse_json(r.stdout.strip())
    except Exception as e:
        print(f"  ❌ Gemini error: {e}"); return None

def _synthesize(ollama: dict, gemini: dict, source: str) -> dict | None:
    prompt = SYNTHESIS_PROMPT.format(
        source=source,
        ollama=json.dumps(ollama, ensure_ascii=False),
        gemini=json.dumps(gemini, ensure_ascii=False)
    )
    result = _run_gemini(prompt)
    if not result:
        print("  ⚠️  Síntesis falló — usando Gemini solo")
    return result

def extract_intelligence(text: str, source: str, source_type: str) -> dict | None:
    """Dispatcher: ollama | gemini | max según BACKEND."""
    prompt = build_prompt(source_type, source, text)

    if BACKEND == "gemini":
        print(f"🟣 Gemini analizando [{source_type.upper()}] {source}...")
        return _run_gemini(prompt)

    elif BACKEND == "max":
        print(f"🔥 MAX — Ollama + Gemini en paralelo para {source}...")
        results, lock = {}, threading.Lock()

        def run_ollama():
            out = _run_ollama(prompt)
            if out:
                with lock: results["ollama"] = out
                print(f"  ✅ Ollama: {len(out.get('tools',[]))} herramientas")

        def run_gemini():
            out = _run_gemini(prompt)
            if out:
                with lock: results["gemini"] = out
                print(f"  ✅ Gemini: {len(out.get('tools',[]))} herramientas")

        t1 = threading.Thread(target=run_ollama)
        t2 = threading.Thread(target=run_gemini)
        t1.start(); t2.start()
        t1.join();  t2.join()

        if not results: return None
        if len(results) == 2:
            synth = _synthesize(results["ollama"], results["gemini"], source)
            if synth: return synth
        return results.get("gemini") or results.get("ollama")

    else:  # ollama
        print(f"🧠 Ollama ({MODEL}) analizando [{source_type.upper()}] {source}...")
        return _run_ollama(prompt)

# ==============================================================================
# MERGE — acumula sin borrar historial
# ==============================================================================

def merge_tools(existing: list, new_items: list, today: str) -> list:
    index = {item["name"].lower(): item for item in existing}
    for item in new_items:
        name = item.get("name", "").strip()
        if not name: continue
        key = name.lower()
        m   = int(item.get("mentions", 1))
        if key in index:
            index[key]["count"]     = index[key].get("count", 0) + m
            index[key]["last_seen"] = today
        else:
            index[key] = {"name": name, "count": m, "last_seen": today}
    return sorted(index.values(), key=lambda x: x.get("count", 0), reverse=True)

def merge_questions(existing: list, new_items: list, today: str) -> list:
    index = {item["text"][:60].lower(): item for item in existing}
    for item in new_items:
        text = item.get("text", "").strip()
        if not text: continue
        key = text[:60].lower()
        m   = int(item.get("mentions", 1))
        if key in index:
            index[key]["count"]     = index[key].get("count", 0) + m
            index[key]["last_seen"] = today
        else:
            index[key] = {"text": text, "count": m, "last_seen": today}
    return sorted(index.values(), key=lambda x: x.get("count", 0), reverse=True)

# ==============================================================================
# PROCESAR UNA FUENTE
# ==============================================================================

def process_source(url: str, seen: set, db: dict, today: str) -> tuple[set, int, int]:
    """Scrape → extrae → merge para cualquier tipo de fuente."""
    stype = detect_source_type(url)
    key   = source_key(url)
    print(f"\n🦆 [{stype.upper()}] {url}")

    text, new_hashes, stype = scrape_source(url, seen)

    if not text.strip():
        print(f"  — Sin contenido nuevo")
        return set(), 0, 0

    print(f"  📝 {len(new_hashes)} ítems nuevos")

    intel = extract_intelligence(text, key, stype)
    if not intel:
        return new_hashes, 0, 0

    entry = db.get(key, {"url": url, "source_type": stype,
                         "tools": [], "questions": [], "references": []})
    entry["tools"]      = merge_tools(entry.get("tools", []),
                                      intel.get("tools", []), today)
    entry["questions"]  = merge_questions(entry.get("questions", []),
                                          intel.get("questions", []), today)
    entry["references"] = list(dict.fromkeys(
        entry.get("references", []) + intel.get("references", [])
    ))[:20]
    entry["last_update"] = datetime.now().strftime("%Y-%m-%d %H:%M")
    entry["source_type"] = stype
    db[key] = entry

    n_tools = len(intel.get("tools", []))
    n_qs    = len(intel.get("questions", []))
    print(f"  ✅ {n_tools} herramientas · {n_qs} tendencias detectadas")
    return new_hashes, n_tools, n_qs

# ==============================================================================
# RADAR — selección de fuentes desde Perry
# ==============================================================================

def get_radar_targets() -> list[str]:
    """Lee hot_sources.json de Perry y devuelve TODAS las fuentes (todos los tipos)."""
    if os.path.exists(HOT_FILE):
        try:
            with open(HOT_FILE) as f:
                hot = json.loads(f.read().strip()).get("hot_sources", [])
            targets = [src["url"] for src in hot if src.get("url")]
            if targets:
                by_type: dict[str, int] = {}
                for t in targets:
                    st = detect_source_type(t)
                    by_type[st] = by_type.get(st, 0) + 1
                summary = " · ".join(f"{st.upper()}:{n}" for st, n in by_type.items())
                print(f"📡 RADAR: {len(targets)} fuentes de Perry  [{summary}]")
                return targets[:MAX_RADAR]
        except Exception as e:
            print(f"  ⚠️  No pude leer hot_sources.json: {e}")

    print(f"📡 RADAR: usando lista por defecto ({len(DEFAULT_RADAR)} fuentes)")
    return DEFAULT_RADAR

# ==============================================================================
# BRIEF SEMANAL — consolidación cross-fuente para William
# ==============================================================================

BRIEF_PROMPT = (
    "Eres el director editorial de Keiyi Digital, una agencia de marketing digital "
    "y EdTech orientada a LATAM.\n\n"
    "Basándote en esta inteligencia recopilada esta semana de Reddit, Hacker News, "
    "blogs y GitHub:\n\n"
    "TOP HERRAMIENTAS (más mencionadas en comunidades):\n{tools}\n\n"
    "TOP PREGUNTAS / PAIN-POINTS:\n{questions}\n\n"
    "Genera 6 ángulos editoriales para artículos del blog de keiyi.digital que:\n"
    "- Sean relevantes para LATAM (emprendedores, marketers, creadores de contenido, "
    "estudiantes de habilidades digitales)\n"
    "- Tengan un ángulo práctico y accionable\n"
    "- Aprovechen las herramientas y preguntas detectadas\n"
    "- Mezclen idiomas si aplica (español principalmente, inglés técnico cuando necesario)\n\n"
    "Output ONLY valid JSON, sin markdown:\n"
    '{{"angles":['
    '{{"title":"string","hook":"string en 2 oraciones",'
    '"target_audience":"string","main_topic":"string","urgency":"alta|media|baja"}}'
    ']}}'
)

def generate_brief(db: dict) -> None:
    """
    Consolida research_db.json en weekly_brief.json.
    Agrega herramientas y preguntas cross-fuente, luego pide ángulos editoriales a Gemini.
    """
    print(f"\n{'─'*55}")
    print("📋 GENERANDO BRIEF SEMANAL...")

    today = datetime.now().strftime("%Y-%m-%d")
    now   = datetime.now().strftime("%Y-%m-%d %H:%M")

    # ── Consolidar herramientas cross-fuente ─────────────────────────────────
    tools_index: dict[str, dict] = {}
    questions_index: dict[str, dict] = {}

    for key, entry in db.items():
        stype = entry.get("source_type", "unknown")
        for tool in entry.get("tools", []):
            name = tool.get("name", "").strip()
            if not name or len(name) < 2:
                continue
            k = name.lower()
            if k in tools_index:
                tools_index[k]["count"]     += tool.get("count", 1)
                tools_index[k]["last_seen"]  = max(tools_index[k]["last_seen"],
                                                   tool.get("last_seen", today))
                if key not in tools_index[k]["sources"]:
                    tools_index[k]["sources"].append(key)
            else:
                tools_index[k] = {
                    "name":      name,
                    "count":     tool.get("count", 1),
                    "last_seen": tool.get("last_seen", today),
                    "sources":   [key],
                }

        for q in entry.get("questions", []):
            text = q.get("text", "").strip()
            if not text or len(text) < 10:
                continue
            k = text[:60].lower()
            if k in questions_index:
                questions_index[k]["count"]     += q.get("count", 1)
                questions_index[k]["last_seen"]  = max(questions_index[k]["last_seen"],
                                                       q.get("last_seen", today))
                if key not in questions_index[k]["sources"]:
                    questions_index[k]["sources"].append(key)
            else:
                questions_index[k] = {
                    "text":      text,
                    "count":     q.get("count", 1),
                    "last_seen": q.get("last_seen", today),
                    "sources":   [key],
                }

    top_tools     = sorted(tools_index.values(),
                           key=lambda x: x["count"], reverse=True)[:20]
    top_questions = sorted(questions_index.values(),
                           key=lambda x: x["count"], reverse=True)[:15]

    print(f"  🔧 {len(top_tools)} herramientas consolidadas")
    print(f"  ❓ {len(top_questions)} preguntas consolidadas")

    # ── Pedir ángulos editoriales a Gemini ──────────────────────────────────
    tools_text = "\n".join(
        f"- {t['name']} ({t['count']} menciones · fuentes: {', '.join(t['sources'][:3])})"
        for t in top_tools[:12]
    )
    questions_text = "\n".join(
        f"- {q['text'][:120]} ({q['count']} menciones)"
        for q in top_questions[:10]
    )

    angles = []
    print("  🟣 Gemini generando ángulos editoriales...")
    try:
        prompt = BRIEF_PROMPT.format(tools=tools_text, questions=questions_text)
        r      = subprocess.run([GEMINI_BIN, "-p", prompt],
                                capture_output=True, text=True, timeout=180)
        parsed = _parse_json(r.stdout.strip())
        if parsed and "angles" in parsed:
            angles = parsed["angles"]
            print(f"  ✅ {len(angles)} ángulos editoriales generados")
        else:
            print("  ⚠️  Gemini no devolvió ángulos válidos")
    except Exception as e:
        print(f"  ⚠️  Error generando ángulos: {e}")

    # ── Resumen de cobertura por tipo de fuente ──────────────────────────────
    coverage: dict[str, int] = {}
    for entry in db.values():
        st = entry.get("source_type", "unknown")
        coverage[st] = coverage.get(st, 0) + 1

    # ── Escribir brief ───────────────────────────────────────────────────────
    brief = {
        "generated_at":    now,
        "period":          today,
        "source_coverage": coverage,
        "sources_total":   len(db),
        "top_tools":       top_tools,
        "top_questions":   top_questions,
        "editorial_angles": angles,
    }

    with open(WEEKLY_BRIEF, "w", encoding="utf-8") as f:
        json.dump(brief, f, ensure_ascii=False, indent=2)

    print(f"  💾 Brief guardado → weekly_brief.json")
    print(f"     {len(top_tools)} herramientas · {len(top_questions)} preguntas · {len(angles)} ángulos editoriales")

# ==============================================================================
# MAIN
# ==============================================================================

def main():
    arg = sys.argv[1] if len(sys.argv) > 1 else None

    if arg:
        targets = [arg]
        print(f"\n🦆 DIPPER · EXCAVACIÓN → {arg} [{detect_source_type(arg).upper()}]")
    else:
        targets = get_radar_targets()
        print(f"\n🦆 DIPPER · MODO RADAR → {len(targets)} fuentes")

    print(f"   Backend : {BACKEND.upper()} · Modelo: {MODEL}")
    print(f"   DB      : {RESEARCH_DB}\n")

    seen  = load_seen()
    today = datetime.now().strftime("%Y-%m-%d")
    db    = {}
    if os.path.exists(RESEARCH_DB):
        with open(RESEARCH_DB, encoding="utf-8") as f:
            db = json.load(f)

    total_hashes, total_tools, total_qs = set(), 0, 0
    for url in targets:
        new_hashes, n_tools, n_qs = process_source(url, seen, db, today)
        total_hashes |= new_hashes
        total_tools  += n_tools
        total_qs     += n_qs
        seen         |= new_hashes

    with open(RESEARCH_DB, "w", encoding="utf-8") as f:
        json.dump(db, f, ensure_ascii=False, indent=2)
    save_seen(seen)

    # Generar brief consolidado solo en modo RADAR (no en excavaciones puntuales)
    if not arg:
        generate_brief(db)

    print(f"\n{'='*55}")
    print(f"🏁 DIPPER COMPLETO")
    print(f"   Fuentes procesadas     : {len(targets)}")
    print(f"   Ítems nuevos vistos    : {len(total_hashes)}")
    print(f"   Herramientas detectadas: {total_tools}")
    print(f"   Tendencias detectadas  : {total_qs}")
    print(f"   DB            → research_db.json")
    print(f"   Brief semanal → weekly_brief.json")
    print(f"{'='*55}")

if __name__ == "__main__":
    main()
