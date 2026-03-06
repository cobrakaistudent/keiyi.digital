#!/usr/bin/env python3
# Keiyi Deep Scout — Reddit Intelligence Crawler
# Dependencias: pip install -r requirements.txt
#
# Propósito: Escarbar Reddit en profundidad (posts top + nuevos + controversiales
# + sus comentarios) para extraer herramientas recomendadas, preguntas frecuentes
# y referencias externas. Registra todo en research_db.json sin repetir posts ya
# procesados (anti-duplicados por post ID).

import os
import json
import re
import time
import requests
from datetime import datetime

AGENT_DIR        = os.path.dirname(__file__)
DEEP_SOURCES_PATH = os.path.join(AGENT_DIR, 'deep_sources.json')
RESEARCH_DB_PATH  = os.path.join(AGENT_DIR, 'research_db.json')
SCRAPED_IDS_PATH  = os.path.join(AGENT_DIR, 'scraped_ids.json')
OLLAMA_API_URL    = "http://localhost:11434/api/generate"

BROWSER_HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Accept": "application/json",
}

# ──────────────────────────────────────────────────────────────────────────────
# UTILIDADES DE ARCHIVO
# ──────────────────────────────────────────────────────────────────────────────

def load_json(path, default):
    try:
        with open(path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except (FileNotFoundError, json.JSONDecodeError):
        return default

def save_json(path, data):
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

# ──────────────────────────────────────────────────────────────────────────────
# REDDIT API (JSON público, sin autenticación)
# ──────────────────────────────────────────────────────────────────────────────

def fetch_reddit_posts(subreddit, sort='top', time_filter='week', limit=25):
    """Descarga posts de un subreddit usando la API JSON pública de Reddit."""
    url = f"https://www.reddit.com/r/{subreddit}/{sort}.json?limit={limit}"
    if sort in ['top', 'controversial']:
        url += f"&t={time_filter}"
    try:
        res = requests.get(url, headers=BROWSER_HEADERS, timeout=15)
        res.raise_for_status()
        children = res.json().get('data', {}).get('children', [])
        return [c['data'] for c in children if c.get('data')]
    except Exception as e:
        print(f"   ⚠️  Error en r/{subreddit}/{sort}: {e}")
        return []

def fetch_post_comments(subreddit, post_id, limit=15):
    """Descarga los comentarios más relevantes de un post."""
    url = f"https://www.reddit.com/r/{subreddit}/comments/{post_id}.json?limit={limit}&depth=2"
    try:
        res = requests.get(url, headers=BROWSER_HEADERS, timeout=15)
        res.raise_for_status()
        data = res.json()
        if len(data) < 2:
            return []
        comments = data[1].get('data', {}).get('children', [])
        texts = []
        for c in comments:
            body = c.get('data', {}).get('body', '')
            if body and body not in ('[deleted]', '[removed]') and len(body) > 20:
                texts.append(body)
        return texts
    except Exception as e:
        print(f"   ⚠️  Error en comentarios del post {post_id}: {e}")
        return []

# ──────────────────────────────────────────────────────────────────────────────
# EXTRACCIÓN DE INTELIGENCIA
# ──────────────────────────────────────────────────────────────────────────────

def extract_urls(text):
    """Extrae URLs externas del texto (excluye Reddit e imágenes)."""
    pattern = re.compile(r'https?://[^\s\)>\"\]]+')
    urls = pattern.findall(text)
    filtered = []
    for u in urls:
        u = u.rstrip('.,;:)\']')
        if 'reddit.com' in u:
            continue
        if re.search(r'\.(jpg|jpeg|png|gif|webp|svg)$', u, re.I):
            continue
        filtered.append(u)
    return filtered

def is_question(text):
    """Detecta si un título es una pregunta."""
    text = text.strip()
    if text.endswith('?'):
        return True
    starters = ['how', 'what', 'why', 'which', 'where', 'when', 'who',
                 'is there', 'does anyone', 'can someone', 'should i',
                 'best way', 'looking for', 'any recommendations',
                 'anyone use', 'thoughts on', 'opinions on']
    return any(text.lower().startswith(s) for s in starters)

def update_counter(db_dict, key, source):
    """Actualiza el contador de una entrada en el registro acumulativo."""
    key = key.strip()
    if not key or len(key) < 3 or len(key) > 300:
        return
    key_lower = key.lower()
    if key_lower not in db_dict:
        db_dict[key_lower] = {'display': key, 'count': 0, 'sources': [], 'last_seen': ''}
    db_dict[key_lower]['count'] += 1
    db_dict[key_lower]['last_seen'] = datetime.now().strftime('%Y-%m-%d')
    if source not in db_dict[key_lower]['sources']:
        db_dict[key_lower]['sources'].append(source)

# ──────────────────────────────────────────────────────────────────────────────
# TÉCNICA ACTIVA: T2 — Batch x5
# Ver agent/DEEP_SCOUT_TECHNIQUES.md para comparativa de técnicas
# ──────────────────────────────────────────────────────────────────────────────
ACTIVE_TECHNIQUE = "T2_batch"
BATCH_SIZE = 5        # Posts por llamada a Ollama
CHARS_PER_POST = 800  # Chars máx por post dentro del batch (~4000 total)

def extract_tools_batch_ollama(batch_posts, source_label, research_db):
    """
    T2: Envía hasta BATCH_SIZE posts en una sola llamada a Ollama.
    Reduce llamadas ~5x vs T1 (per-post).
    batch_posts: lista de strings con el texto de cada post.
    """
    if not batch_posts:
        return 0

    sections = []
    for i, text in enumerate(batch_posts, 1):
        sections.append(f"POST {i}:\n{text[:CHARS_PER_POST]}")
    batch_text = "\n\n---\n\n".join(sections)

    prompt = (
        f"Analiza estos {len(batch_posts)} posts de Reddit e identifica TODAS las herramientas, "
        "software, plataformas SaaS, apps o servicios digitales mencionados en cualquiera de ellos "
        "(ej: HubSpot, Make.com, Canva, Notion, ChatGPT, Mailchimp, etc.). "
        "Ignora marcas genéricas (Google, Apple, Amazon) salvo productos específicos "
        "(Google Analytics, Apple Podcasts, AWS).\n\n"
        f"{batch_text}\n\n"
        'Responde SOLO con JSON: {"tools": ["tool1", "tool2"]} — '
        'si no hay herramientas: {"tools": []}'
    )

    try:
        res = requests.post(OLLAMA_API_URL, json={
            "model": "qwen3:8b",
            "prompt": prompt,
            "format": "json",
            "stream": False
        }, timeout=40)
        res.raise_for_status()
        result = json.loads(res.json().get('response', '{}'))
        tools_found = result.get('tools', [])
        for tool in tools_found:
            update_counter(research_db['tools'], tool, source_label)
        return len(tools_found)
    except Exception:
        return 0  # Silent fail

# ──────────────────────────────────────────────────────────────────────────────
# PROCESAMIENTO DE SUBREDDIT
# ──────────────────────────────────────────────────────────────────────────────

def process_subreddit(subreddit, research_db, scraped_ids):
    """
    Escarba un subreddit en 3 dimensiones:
    - top (semana): los más votados — tendencias consolidadas
    - new: posts recientes — joyas escondidas, señales tempranas
    - controversial (mes): debates activos — puntos de dolor reales
    """
    source_label = f"r/{subreddit}"
    new_count = 0
    skipped_count = 0

    # Recolectar posts de las 3 dimensiones
    all_posts = []
    fetch_plan = [
        ('top',          'week',  25),
        ('new',          None,    25),
        ('controversial','month', 15),
    ]
    for sort, time_f, limit in fetch_plan:
        posts = fetch_reddit_posts(subreddit, sort=sort, time_filter=time_f or 'week', limit=limit)
        all_posts.extend(posts)
        print(f"   📥 /{sort}: {len(posts)} posts")
        time.sleep(1)  # Respetar rate limit de Reddit

    # Deduplicar por ID dentro de esta ejecución
    seen_this_run = set()
    unique_posts = []
    for p in all_posts:
        pid = p.get('id')
        if pid and pid not in seen_this_run:
            seen_this_run.add(pid)
            unique_posts.append(p)

    print(f"   🔎 {len(unique_posts)} posts únicos — procesando...")

    posts_to_analyze = []  # Textos acumulados para batch Ollama (T2)
    for post in unique_posts:
        post_id = post.get('id')
        if not post_id:
            continue

        # Anti-duplicados: saltar posts ya procesados en ejecuciones anteriores
        if post_id in scraped_ids:
            skipped_count += 1
            continue

        title    = post.get('title', '')
        selftext = post.get('selftext', '') or ''
        score    = post.get('score', 0)
        n_comments = post.get('num_comments', 0)

        # Marcar como procesado
        scraped_ids.add(post_id)
        new_count += 1

        full_text = f"{title}\n{selftext}"

        # ── Registrar preguntas ──
        if is_question(title):
            update_counter(research_db['questions'], title, source_label)

        # ── Registrar URLs del post ──
        for url in extract_urls(full_text):
            update_counter(research_db['references'], url, source_label)

        # ── Ir a comentarios en posts con engagement ──
        if score > 3 or n_comments > 2:
            comments = fetch_post_comments(subreddit, post_id)
            for comment in comments:
                full_text += f"\n{comment}"
                for url in extract_urls(comment):
                    update_counter(research_db['references'], url, source_label)
            time.sleep(0.5)  # Rate limit

        # Acumular texto para batch
        posts_to_analyze.append(full_text)

    # ── Extracción batch de herramientas (T2) ──────────────────────────────
    ollama_calls = 0
    tools_total = 0
    for i in range(0, len(posts_to_analyze), BATCH_SIZE):
        batch = posts_to_analyze[i:i + BATCH_SIZE]
        found = extract_tools_batch_ollama(batch, source_label, research_db)
        tools_total += found
        ollama_calls += 1
        print(f"   🧠 Batch {ollama_calls}: {len(batch)} posts → {found} herramientas")

    print(f"   ✅ {new_count} nuevos | {skipped_count} saltados | {ollama_calls} llamadas Ollama | {tools_total} herramientas")
    return new_count

# ──────────────────────────────────────────────────────────────────────────────
# MAIN
# ──────────────────────────────────────────────────────────────────────────────

def main():
    print("=" * 55)
    print(f"🕳️  KEIYI DEEP SCOUT — Reddit Intelligence")
    print(f"   {datetime.now().strftime('%Y-%m-%d %H:%M')}")
    print("=" * 55)

    deep_sources = load_json(DEEP_SOURCES_PATH, [])
    if not deep_sources:
        print("❌ Sin fuentes profundas configuradas. Agrégalas desde el Command Center.")
        return

    research_db = load_json(RESEARCH_DB_PATH, {
        'tools': {},
        'questions': {},
        'references': {},
        'last_updated': ''
    })
    scraped_ids = set(load_json(SCRAPED_IDS_PATH, []))

    t_start = time.time()
    total_new = 0
    for source in deep_sources:
        subreddit = source.get('subreddit', '').strip()
        if not subreddit:
            continue
        print(f"\n🔍 Analizando r/{subreddit}...")
        total_new += process_subreddit(subreddit, research_db, scraped_ids)

    elapsed = round(time.time() - t_start, 1)
    research_db['last_updated'] = datetime.now().strftime('%Y-%m-%d %H:%M')
    save_json(RESEARCH_DB_PATH, research_db)
    save_json(SCRAPED_IDS_PATH, list(scraped_ids))

    print(f"\n{'=' * 55}")
    print(f"🏁 Deep Scout completado [{ACTIVE_TECHNIQUE}] — {elapsed}s")
    print(f"   📬 Posts nuevos analizados : {total_new}")
    print(f"   🛠️  Herramientas trackeadas : {len(research_db['tools'])}")
    print(f"   ❓ Preguntas capturadas    : {len(research_db['questions'])}")
    print(f"   🔗 Referencias registradas : {len(research_db['references'])}")
    print(f"\n[METRICAS_T2] posts={total_new} tiempo={elapsed}s herramientas={len(research_db['tools'])}")

    if research_db['tools']:
        top = sorted(research_db['tools'].items(), key=lambda x: x[1]['count'], reverse=True)[:5]
        print(f"\n🏆 Top 5 herramientas:")
        for name, data in top:
            print(f"   • {data['display']}: {data['count']} menciones")

    # Subir resultados a Google Drive automáticamente
    from google_drive_uploader import upload_to_drive
    upload_to_drive()

    print("=" * 55)


if __name__ == "__main__":
    main()
