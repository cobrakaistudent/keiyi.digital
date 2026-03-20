#!/usr/bin/env python3
"""
PERRY EL ORNITORRINCO — Agente de Reconocimiento de Keiyi Digital
==================================================================
Misión: SCRAPE + DISCOVER. Sin análisis LLM pesado — eso es trabajo de Dipper.

SCRAPE   → descarga agresiva y paralela de todas las fuentes aprobadas
           actualiza scores de actividad → Dipper sabe dónde ir primero
DISCOVER → Claude + Gemini buscan comunidades nuevas globales
           CEO aprueba antes de que entren al radar
"""

import os, sys, json, hashlib, subprocess, threading, requests, re, time
from datetime import datetime
from bs4 import BeautifulSoup
from xml.etree import ElementTree as ET
from urllib.parse import urlparse

# ==============================================================================
# PATHS & CONFIG
# ==============================================================================

AGENT_DIR   = os.path.dirname(os.path.abspath(__file__))
CONFIG_FILE = os.path.join(AGENT_DIR, "perry_config.json")
STATUS_FILE = os.path.join(AGENT_DIR, "perry_status.json")

def load_config() -> dict:
    defaults = {
        "storage_path": os.path.expanduser(
            "~/Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/"
            "My Drive/gemini/keiyi_scout_intelligence"
        ),
        "backend": "max",   # max | claude | gemini — para DESCUBRIR
        "discovery_interval_days": 7,
        "monitor_interval_min": 30,
        "scrape_posts_per_source": 25,
        "conditions": {
            "check_ram":    True,
            "check_cpu":    True,
            "check_idle":   True,
            "max_cpu_pct":  30.0,
            "min_idle_min": 10.0
        }
    }
    if os.path.exists(CONFIG_FILE):
        with open(CONFIG_FILE) as f:
            saved = json.load(f)
        defaults.update(saved)
    return defaults

def update_status(patch: dict):
    try:
        current = {}
        if os.path.exists(STATUS_FILE):
            with open(STATUS_FILE) as f:
                current = json.load(f)
        current.update(patch)
        current["last_update"] = datetime.now().isoformat()
        with open(STATUS_FILE, "w") as f:
            json.dump(current, f, indent=2)
    except:
        pass

def get_paths(cfg: dict) -> dict:
    base = cfg["storage_path"]
    return {
        "base":     base,
        "sources":  os.path.join(base, "sources_radar.json"),
        "raw":      os.path.join(base, "raw_cache"),
        "hot":      os.path.join(base, "hot_sources.json"),
        "chat":     os.path.join(base, "perry_chat.json"),
        "seen":     os.path.join(base, "seen_comments.json"),
    }

def ensure_dirs(paths: dict):
    os.makedirs(paths["base"], exist_ok=True)
    os.makedirs(paths["raw"],  exist_ok=True)

# ==============================================================================
# SOURCES MANAGEMENT
# ==============================================================================

def load_sources(paths: dict) -> list:
    if not os.path.exists(paths["sources"]):
        return []
    with open(paths["sources"]) as f:
        return json.load(f)

def save_sources(sources: list, paths: dict):
    with open(paths["sources"], "w") as f:
        json.dump(sources, f, ensure_ascii=False, indent=2)

def approved_sources(paths: dict) -> list:
    return [s for s in load_sources(paths) if s.get("status") == "approved"]

def add_pending_source(url: str, reason: str, paths: dict):
    safe, msg = is_safe_url(url)
    if not safe:
        print(f"  🚫 URL bloqueada ({msg}): {url}")
        return
    sources = load_sources(paths)
    existing = [s["url"] for s in sources]
    if url not in existing:
        sources.append({
            "url": url,
            "status": "pending",
            "reason": reason,
            "discovered_at": datetime.now().isoformat(),
            "relevance_score": None,
            "language": None,
            "last_checked": None
        })
        save_sources(sources, paths)

# ==============================================================================
# SAFETY — Perry no entra a páginas peligrosas
# ==============================================================================

# Patrones que Perry rechaza automáticamente
BLOCKED_PATTERNS = [
    r"\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}",   # URLs con IP directa
    r"bit\.ly|tinyurl\.com|t\.co|ow\.ly|goo\.gl|rb\.gy",  # acortadores (ocultan destino)
    r"\.(tk|pw|ml|ga|cf|gq|xyz|top|icu|club|loan|click|download)(/|$)",  # TLDs abusados
    r"(crack|keygen|serial|torrent|warez|pirat)",  # piratería
    r"(porn|adult|xxx|nsfw|nude|sex\.)",           # contenido adulto
    r"(casino|poker|betting|slots|gambling)",       # apuestas
    r"(malware|phishing|scam|spam|trojan|ransomware)",  # explícitamente malicioso
    r"(free.*download|download.*free.*full|crack.*download)",  # software pirata
    r"^javascript:|^data:|^vbscript:",             # esquemas peligrosos
]

ALLOWED_SCHEMES = {"http", "https"}

def is_safe_url(url: str) -> tuple[bool, str]:
    """Verifica si una URL es segura para scrape. Retorna (safe, motivo)."""
    url_lower = url.lower().strip()

    # Subreddits en formato r/name — siempre seguros
    if re.match(r"^r/[\w]+$", url_lower):
        return True, "Subreddit format"

    # Verificar esquema
    try:
        parsed = urlparse(url)
        if parsed.scheme not in ALLOWED_SCHEMES:
            return False, f"Esquema no permitido: {parsed.scheme}"
    except Exception:
        return False, "URL inválida"

    # Verificar patrones bloqueados
    for pattern in BLOCKED_PATTERNS:
        if re.search(pattern, url_lower):
            return False, f"Patrón bloqueado: {pattern}"

    return True, "OK"

# ==============================================================================
# ANTI-REDUNDANCIA
# ==============================================================================

def load_seen(paths: dict) -> set:
    if not os.path.exists(paths["seen"]):
        return set()
    with open(paths["seen"]) as f:
        return set(json.load(f))

def save_seen(seen: set, paths: dict):
    with open(paths["seen"], "w") as f:
        json.dump(list(seen), f)

def hash_text(text: str) -> str:
    return hashlib.md5(text.strip().encode()).hexdigest()

# ==============================================================================
# SCRAPE — Agresivo y paralelo
# ==============================================================================

CLAUDE_BIN = "/Users/anuarlv/.local/bin/claude"
GEMINI_BIN = "/opt/homebrew/bin/gemini"

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
                  "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
}

def scrape_reddit(sub: str, seen: set, limit: int) -> tuple[str, set, dict]:
    """
    Scrape both /hot and /top from a subreddit.
    Returns (text, new_hashes, activity_metrics).
    """
    texts      = []
    new_hashes = set()
    total_ups  = 0
    total_cmts = 0
    post_count = 0

    for sort, params in [("hot", f"limit={limit}"), ("top", f"limit={limit}&t=week")]:
        try:
            r = requests.get(
                f"https://www.reddit.com/r/{sub}/{sort}.json?{params}",
                headers=HEADERS, timeout=15
            )
            if r.status_code != 200:
                continue
            posts = r.json()["data"]["children"]
            for p in posts:
                d = p["data"]
                # Skip deleted/removed posts
                if d.get("removed_by_category") or d.get("selftext") == "[removed]":
                    continue
                text = f"{d['title']} {d.get('selftext', '')[:800]}"
                h = hash_text(text)
                if h not in seen:
                    new_hashes.add(h)
                    texts.append(text)
                    total_ups  += d.get("ups", 0)
                    total_cmts += d.get("num_comments", 0)
                    post_count += 1
        except Exception as e:
            print(f"    ⚠️  Reddit {sub}/{sort}: {e}")

    activity = {
        "new_posts":      post_count,
        "avg_upvotes":    round(total_ups / max(post_count, 1)),
        "avg_comments":   round(total_cmts / max(post_count, 1)),
        "engagement":     round((total_ups + total_cmts * 3) / max(post_count, 1)),
    }
    return "\n\n".join(texts), new_hashes, activity

def scrape_generic(url: str, seen: set, limit: int) -> tuple[str, set, dict]:
    """
    Scrape a generic URL: tries RSS first, then HTML.
    Returns (text, new_hashes, activity_metrics).
    """
    texts      = []
    new_hashes = set()
    post_count = 0

    try:
        r = requests.get(url, headers=HEADERS, timeout=15, allow_redirects=True)

        # Detect suspicious redirect
        if r.url != url:
            final_safe, msg = is_safe_url(r.url)
            if not final_safe:
                print(f"    🚫 Redirect peligroso a {r.url}: {msg}")
                return "", set(), {"new_posts": 0}

        # Try RSS/Atom first
        try:
            root  = ET.fromstring(r.content)
            items = root.findall(".//item") or root.findall(".//entry")
            for item in items[:limit]:
                t    = item.find("title")
                desc = item.find("description") or item.find("summary")
                pub  = item.find("pubDate") or item.find("updated")
                text = (
                    f"{t.text if t is not None else ''} "
                    f"{desc.text if desc is not None else ''}"
                )[:800]
                h = hash_text(text)
                if h not in seen:
                    new_hashes.add(h)
                    texts.append(text)
                    post_count += 1
        except ET.ParseError:
            # Fallback: HTML scraping
            soup = BeautifulSoup(r.text, "html.parser")
            for tag in soup(["script", "style", "nav", "footer", "header",
                             "aside", "form", "button", "iframe"]):
                tag.decompose()
            text = " ".join(soup.get_text().split())[:5000]
            h = hash_text(text)
            if h not in seen:
                new_hashes.add(h)
                texts.append(text)
                post_count = 1

    except requests.exceptions.SSLError:
        print(f"    🚫 SSL inválido: {url}")
        return "", set(), {"new_posts": 0}
    except Exception as e:
        print(f"    ⚠️  Error scraping {url}: {e}")

    activity = {"new_posts": post_count, "avg_upvotes": 0, "avg_comments": 0, "engagement": post_count * 10}
    return "\n\n".join(texts), new_hashes, activity

def scrape_source(source: dict, seen: set, limit: int) -> tuple[str, set, dict]:
    """Dispatcher: Reddit vs generic URL."""
    url  = source["url"]
    safe, msg = is_safe_url(url)
    if not safe:
        print(f"    🚫 Bloqueada ({msg}): {url}")
        return "", set(), {"new_posts": 0}

    if "reddit.com/r/" in url or re.match(r"^r/[\w]+$", url):
        sub = url.strip("/").split("/")[-1]
        return scrape_reddit(sub, seen, limit)
    else:
        return scrape_generic(url, seen, limit)

def update_source_scores(sources: list, activity_by_url: dict, paths: dict):
    """
    Actualiza relevance_score, velocity y last_checked en sources_radar.json.
    Carga TODAS las fuentes (approved + pending) para no perder las pendientes.
    """
    # Cargar todas las fuentes para preservar pending
    all_sources = load_sources(paths)
    url_index   = {s["url"]: s for s in all_sources}

    for src in sources:
        url = src["url"]
        if url not in activity_by_url:
            continue
        act        = activity_by_url[url]
        new_posts  = act.get("new_posts", 0)
        engagement = act.get("engagement", 0)
        raw_score  = min(100, engagement * 2 + new_posts * 5)
        old_score  = src.get("relevance_score") or 50
        updated    = {
            "relevance_score": round(old_score * 0.6 + raw_score * 0.4, 1),
            "last_checked":    datetime.now().isoformat(),
            "new_posts_last":  new_posts,
            "avg_engagement":  act.get("avg_upvotes", 0) + act.get("avg_comments", 0),
        }
        if url in url_index:
            url_index[url].update(updated)

    save_sources(list(url_index.values()), paths)

def run_scrape(paths: dict, cfg: dict) -> dict:
    """
    Scrape paralelo de todas las fuentes aprobadas.
    Actualiza scores de actividad al terminar.
    Retorna {source_url: raw_text} con el contenido nuevo.
    """
    sources = approved_sources(paths)
    seen    = load_seen(paths)
    limit   = cfg.get("scrape_posts_per_source", 25)

    if not sources:
        print("  📭 Sin fuentes aprobadas. Agrega fuentes primero.")
        return {}

    print(f"\n📡 PERRY · SCRAPE AGRESIVO ({len(sources)} fuentes · {limit} posts c/u · paralelo)")
    update_status({"phase": "scrape", "sources_total": len(sources)})

    results      = {}
    new_seen     = set()
    activity_map = {}
    lock         = threading.Lock()

    def scrape_one(src):
        label = src["url"]
        print(f"  → {label}")
        text, hashes, activity = scrape_source(src, seen, limit)
        with lock:
            if text.strip():
                results[label] = text
                new_seen.update(hashes)
                # Guardar raw cache
                safe_name = re.sub(r"[^\w]", "_", label)[:55]
                ts        = datetime.now().strftime("%Y%m%d_%H%M%S")
                raw_file  = os.path.join(paths["raw"], f"{safe_name}_{ts}.txt")
                with open(raw_file, "w") as f:
                    f.write(f"SOURCE: {label}\n\n{text}")
                print(f"     ✅ {activity['new_posts']} posts nuevos "
                      f"(engagement avg: {activity.get('avg_upvotes',0)}↑ "
                      f"{activity.get('avg_comments',0)}💬)")
            else:
                print(f"     — Sin contenido nuevo")
            activity_map[label] = activity

    # Lanzar todos en paralelo
    threads = [threading.Thread(target=scrape_one, args=(src,)) for src in sources]
    for t in threads: t.start()
    for t in threads: t.join()

    # Actualizar hashes y scores
    seen |= new_seen
    save_seen(seen, paths)
    update_source_scores(sources, activity_map, paths)

    # Guardar resumen de fuentes calientes para Dipper
    hot = sorted(
        [{"url": u, **a} for u, a in activity_map.items() if a.get("new_posts", 0) > 0],
        key=lambda x: x.get("engagement", 0),
        reverse=True
    )
    with open(paths["hot"], "w") as f:
        json.dump({"generated_at": datetime.now().isoformat(), "hot_sources": hot}, f, indent=2)

    total_new = sum(a.get("new_posts", 0) for a in activity_map.values())
    print(f"\n  ✅ Scrape completo: {len(results)} fuentes activas · {total_new} posts nuevos")
    print(f"  🔥 Más activa: {hot[0]['url'] if hot else 'N/A'}")
    update_status({"phase": "idle", "last_scrape": datetime.now().isoformat(),
                   "last_new_posts": total_new, "hot_source": hot[0]["url"] if hot else ""})
    return results

# ==============================================================================
# DISCOVER — Perry busca fuentes nuevas con Claude + Gemini
# ==============================================================================

DISCOVER_PROMPT = """You are Perry, a reconnaissance agent for Keiyi Digital (digital marketing + AI education agency based in Mexico/Latin America).

Find the BEST online communities worldwide for monitoring trends in:
- Digital marketing, growth hacking, social media strategy
- AI tools, automation, no-code/low-code
- Online education, EdTech, course creation
- SaaS, agency business models, freelancing
- Content creation, video, newsletters

CRITICAL RULES:
- Search across ALL languages and regions — Spanish, Portuguese, English, French, German, Japanese, etc.
- Include Reddit subreddits, forums, Discord communities, Hacker News, newsletters, industry blogs, university sites
- EXCLUDE: sites with paywalls, adult content, gambling, piracy, or low-quality content farms
- PREFER: communities with active discussion (daily posts), expert members, and high signal-to-noise ratio

Already monitoring: {existing_note}

Return ONLY valid JSON:
{{
  "communities": [
    {{
      "url": "r/subreddit OR https://full-url",
      "name": "Community name",
      "language": "en/es/pt/fr/de/ja/etc",
      "region": "US/MX/BR/ES/Global/etc",
      "type": "reddit|forum|newsletter|blog|news|discord",
      "why": "One sentence: why this matters for a digital marketing + AI education agency",
      "estimated_signal_quality": 0-100,
      "post_frequency": "daily|weekly|irregular"
    }}
  ]
}}"""

def run_discover(paths: dict, cfg: dict):
    """
    Busca comunidades nuevas con Claude + Gemini en paralelo.
    Filtra por seguridad antes de agregar. CEO aprueba cada una.
    """
    print(f"\n🌐 PERRY · DISCOVER — Buscando comunidades globales")
    update_status({"phase": "discover"})

    sources  = load_sources(paths)
    existing = [s["url"] for s in sources]
    note     = ", ".join(existing[:15]) if existing else "ninguna aún"
    prompt   = DISCOVER_PROMPT.format(existing_note=note)

    findings = []
    errors   = []

    def _claude():
        try:
            env = {**os.environ, "CLAUDECODE": ""}
            r   = subprocess.run(
                [CLAUDE_BIN, "-p", "--output-format", "text"],
                input=prompt, capture_output=True, text=True, timeout=300, env=env
            )
            raw = r.stdout.strip()
            if not raw:
                err = r.stderr.strip().splitlines()[-1] if r.stderr.strip() else "sin output"
                raise ValueError(f"Respuesta vacía (exit={r.returncode}) — {err}")
            match = re.search(r"```(?:json)?(.*?)```", raw, re.DOTALL)
            if match:
                raw = match.group(1).strip()
            s = raw.find("{"); e = raw.rfind("}")
            if s != -1 and e != -1:
                raw = raw[s:e+1]
            findings.append(json.loads(raw))
            print("  ✅ Claude: respuesta recibida")
        except Exception as e:
            errors.append(f"Claude: {e}")
            print(f"  ⚠️  Claude error: {e}")

    def _gemini():
        try:
            r   = subprocess.run(
                [GEMINI_BIN, "-p", prompt],
                capture_output=True, text=True, timeout=300
            )
            raw = r.stdout.strip()
            if not raw:
                err = r.stderr.strip().splitlines()[-1] if r.stderr.strip() else "sin output"
                raise ValueError(f"Respuesta vacía (exit={r.returncode}) — {err}")
            # Extraer JSON robusto: code fence → luego buscar { ... }
            match = re.search(r"```(?:json)?(.*?)```", raw, re.DOTALL)
            if match:
                raw = match.group(1).strip()
            s = raw.find("{"); e = raw.rfind("}")
            if s != -1 and e != -1:
                raw = raw[s:e+1]
            findings.append(json.loads(raw))
            print("  ✅ Gemini: respuesta recibida")
        except Exception as e:
            errors.append(f"Gemini: {e}")
            print(f"  ⚠️  Gemini error: {e}")

    backends = cfg.get("backend", "gemini")
    threads  = []
    if backends == "claude": threads.append(threading.Thread(target=_claude))
    if backends in ("max", "gemini"): threads.append(threading.Thread(target=_gemini))
    for t in threads: t.start()
    for t in threads: t.join()

    if not findings:
        print("  ❌ Ningún backend respondió. Revisa que Claude y Gemini CLI estén instalados.")
        update_status({"phase": "idle"})
        return

    # Merge + dedup + safety filter
    seen_urls = set(existing)
    added = blocked = duplicates = 0

    for result in findings:
        for c in result.get("communities", []):
            url = c.get("url", "").strip()
            if not url:
                continue
            if url in seen_urls:
                duplicates += 1
                continue

            safe, reason = is_safe_url(url)
            if not safe:
                blocked += 1
                print(f"  🚫 Bloqueada [{reason}]: {url}")
                continue

            # Quality threshold — skip obvious low-signal suggestions
            if c.get("estimated_signal_quality", 100) < 40:
                print(f"  ⚠️  Baja calidad ({c['estimated_signal_quality']}): {url}")
                continue

            seen_urls.add(url)
            add_pending_source(
                url,
                f"{c.get('why','')} | Calidad: {c.get('estimated_signal_quality','?')} | "
                f"Lang: {c.get('language','?')} | Region: {c.get('region','?')} | "
                f"Frecuencia: {c.get('post_frequency','?')}",
                paths
            )
            added += 1
            print(f"  + Pendiente: [{c.get('language','?')}·{c.get('region','?')}] {url}")

    print(f"\n  ✅ {added} nuevas fuentes pendientes · {blocked} bloqueadas · {duplicates} duplicadas")
    update_status({"phase": "idle", "last_discover": datetime.now().isoformat(),
                   "last_discover_added": added})

# ==============================================================================
# CHAT — CEO habla con Perry
# ==============================================================================

def build_context(paths: dict) -> str:
    ctx  = "=== KEIYI DIGITAL — PERRY CONTEXT ===\n"
    ctx += "Agency: Digital marketing + AI education (Mexico/LATAM). CEO needs actionable intelligence.\n\n"

    sources = approved_sources(paths)
    if sources:
        # Sort by activity
        top = sorted(sources, key=lambda s: s.get("relevance_score") or 0, reverse=True)[:10]
        ctx += f"TOP ACTIVE SOURCES ({len(sources)} total):\n"
        for s in top:
            ctx += (f"  - {s['url']} "
                    f"(score: {s.get('relevance_score','?')} · "
                    f"new_posts: {s.get('new_posts_last','?')})\n")
        ctx += "\n"

    if os.path.exists(paths["hot"]):
        with open(paths["hot"]) as f:
            hot_data = json.load(f)
        hot = hot_data.get("hot_sources", [])[:5]
        if hot:
            ctx += "HOT SOURCES RIGHT NOW:\n"
            for h in hot:
                ctx += f"  - {h['url']} ({h.get('new_posts',0)} new posts · engagement {h.get('engagement',0)})\n"
            ctx += "\n"

    return ctx

def chat(question: str, paths: dict):
    print(f"\n💬 PERRY · CHAT")
    print(f"  CEO: {question}\n")

    context     = build_context(paths)
    full_prompt = (f"{context}\nCEO QUESTION: {question}\n\n"
                   "Answer as Perry — concise, actionable, data-driven. "
                   "Focus on what the data shows, not speculation.")
    responses = {}

    def _claude():
        try:
            env = {**os.environ, "CLAUDECODE": ""}
            r   = subprocess.run(
                [CLAUDE_BIN, "-p", "--output-format", "text"],
                input=full_prompt, capture_output=True, text=True, timeout=60, env=env)
            responses["claude"] = r.stdout.strip()
        except Exception as e:
            responses["claude"] = f"Error: {e}"

    def _gemini():
        try:
            r = subprocess.run([GEMINI_BIN, "-p", full_prompt],
                               capture_output=True, text=True, timeout=150)
            responses["gemini"] = r.stdout.strip()
        except Exception as e:
            responses["gemini"] = f"Error: {e}"

    threads = [threading.Thread(target=_gemini)]
    for t in threads: t.start()
    for t in threads: t.join()

    final = responses.get("gemini", "Sin respuesta disponible.")

    # Guardar historial
    history = []
    if os.path.exists(paths["chat"]):
        with open(paths["chat"]) as f:
            history = json.load(f)
    history.append({
        "ts": datetime.now().isoformat(),
        "question": question,
        "response": final,
        "backends_used": list(responses.keys())
    })
    with open(paths["chat"], "w") as f:
        json.dump(history[-50:], f, ensure_ascii=False, indent=2)

    print(f"  🦆 Perry: {final}")
    return final

# ==============================================================================
# STORAGE INFO
# ==============================================================================

def get_storage_info(paths: dict) -> dict:
    info  = {}
    files = {
        "sources_radar.json": paths["sources"],
        "hot_sources.json":   paths["hot"],
        "perry_chat.json":    paths["chat"],
        "seen_comments.json": paths["seen"],
        "raw_cache/":         paths["raw"],
    }
    for name, path in files.items():
        if os.path.isdir(path):
            total = sum(
                os.path.getsize(os.path.join(path, fn))
                for fn in os.listdir(path) if os.path.isfile(os.path.join(path, fn))
            )
            info[name] = {"size_mb": round(total / 1024 / 1024, 2),
                          "files": len(os.listdir(path))}
        elif os.path.exists(path):
            info[name] = {
                "size_mb":  round(os.path.getsize(path) / 1024 / 1024, 3),
                "modified": datetime.fromtimestamp(os.path.getmtime(path)).isoformat()
            }
        else:
            info[name] = {"size_mb": 0, "exists": False}
    return info

# ==============================================================================
# MAIN
# ==============================================================================

def main():
    cfg   = load_config()
    paths = get_paths(cfg)
    ensure_dirs(paths)

    cmd = sys.argv[1] if len(sys.argv) > 1 else "scrape"

    if cmd == "scrape":
        run_scrape(paths, cfg)

    elif cmd == "discover":
        run_discover(paths, cfg)

    elif cmd == "chat":
        question = " ".join(sys.argv[2:]) if len(sys.argv) > 2 else ""
        if not question:
            print("Uso: perry.py chat <pregunta>")
            return
        chat(question, paths)

    elif cmd == "storage":
        print(json.dumps(get_storage_info(paths), indent=2))

    else:
        print("Comandos: scrape | discover | chat <pregunta> | storage")
        print("")
        print("Perry hace:")
        print("  scrape   → descarga agresiva de todas las fuentes aprobadas")
        print("  discover → busca comunidades nuevas globales (Claude + Gemini)")
        print("  chat     → CEO pregunta, Perry responde con contexto")
        print("")
        print("Perry NO hace análisis LLM pesado — eso es trabajo de Dipper.")

if __name__ == "__main__":
    main()
