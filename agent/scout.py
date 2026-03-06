#!/usr/bin/env python3
# Dependencias: pip install -r requirements.txt
import os
import requests
import json
import subprocess
import xml.etree.ElementTree as ET
from datetime import datetime

try:
    from bs4 import BeautifulSoup
    BS4_AVAILABLE = True
except ImportError:
    BS4_AVAILABLE = False
    print("⚠️  ADVERTENCIA: beautifulsoup4 no instalada. Las fuentes tipo 'web' serán omitidas.")
    print("   Solución: pip install -r agent/requirements.txt")

# ==============================================================================
# KEIYI SCOUT AI - CEREBRO LOCAL (Mac M2)
# ==============================================================================
# Propósito: Ejecutar carga pesada de Análisis AI mediante Ollama local para
# ahorrar recursos de servidor en Hostinger y asegurar máxima privacidad de datos.
#
# Tipos de fuente soportados:
#   rss/sitemap  → extract_rss_headlines()   — feeds XML estándar
#   web          → extract_web_content()     — deep scrape de HTML (currículas)
# ==============================================================================

USE_SSH = False # Cambiar a True cuando la BD hostinger esté sincronizada
OLLAMA_API_URL = "http://localhost:11434/api/generate"

# Fuentes predeterminadas — se usan cuando no hay fuentes configuradas en Hostinger
DEFAULT_SOURCES = [
    # ── EdTech & Universidades ─────────────────────────────────────────────
    {"name": "r/edtech (Reddit)",           "url": "https://www.reddit.com/r/edtech/.rss",               "type": "rss"},
    {"name": "MIT News — Education",        "url": "https://news.mit.edu/rss/topic/education",            "type": "rss"},
    {"name": "Stanford News",               "url": "https://news.stanford.edu/feed/",                     "type": "rss"},
    {"name": "TechCrunch Education",        "url": "https://techcrunch.com/category/education/feed/",     "type": "rss"},
    {"name": "EdSurge News",                "url": "https://www.edsurge.com/news.rss",                    "type": "rss"},
    {"name": "Harvard Business School",     "url": "https://www.hbs.edu/news/rss/Pages/default.aspx",    "type": "rss"},
    # ── AI & Tecnología ────────────────────────────────────────────────────
    {"name": "r/artificial (Reddit)",       "url": "https://www.reddit.com/r/artificial/.rss",           "type": "rss"},
    {"name": "r/MachineLearning (Reddit)",  "url": "https://www.reddit.com/r/MachineLearning/.rss",      "type": "rss"},
    {"name": "VentureBeat AI",              "url": "https://venturebeat.com/category/ai/feed/",           "type": "rss"},
    {"name": "TechCrunch AI",               "url": "https://techcrunch.com/category/artificial-intelligence/feed/", "type": "rss"},
    # ── Marketing Digital & AI Marketing ──────────────────────────────────
    {"name": "r/digital_marketing (Reddit)","url": "https://www.reddit.com/r/digital_marketing/.rss",    "type": "rss"},
    {"name": "r/marketing (Reddit)",        "url": "https://www.reddit.com/r/marketing/.rss",             "type": "rss"},
    {"name": "r/SEO (Reddit)",              "url": "https://www.reddit.com/r/SEO/.rss",                   "type": "rss"},
    {"name": "Marketing AI Institute",      "url": "https://www.marketingaiinstitute.com/blog/rss.xml",   "type": "rss"},
    {"name": "HubSpot Marketing Blog",      "url": "https://blog.hubspot.com/marketing/rss.xml",          "type": "rss"},
    {"name": "Neil Patel Blog",             "url": "https://neilpatel.com/blog/feed/",                    "type": "rss"},
    {"name": "Search Engine Journal",       "url": "https://www.searchenginejournal.com/feed/",           "type": "rss"},
    {"name": "Content Marketing Institute", "url": "https://contentmarketinginstitute.com/feed/",         "type": "rss"},
    {"name": "MarTech Series",              "url": "https://martechseries.com/feed/",                     "type": "rss"},
    {"name": "Social Media Examiner",       "url": "https://www.socialmediaexaminer.com/feed/",           "type": "rss"},
]

# Headers de navegador real para evitar bloqueos básicos anti-bot
BROWSER_HEADERS = {
    "User-Agent":      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Accept":          "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Language": "es-MX,es;q=0.9,en;q=0.8",
    "Accept-Encoding": "gzip, deflate, br",
    "DNT":             "1",
    "Connection":      "keep-alive",
}

def get_pending_sources():
    """Descarga la agenda de vigilancia directamente de la BD (Vía SSH/Local)."""
    print("📡 Extrayendo inteligencia cruda de la Base de Datos...")
    
    php_code = r"echo json_encode(\App\Models\ScoutSource::where('is_active', true)->get());"
    php_template = f"require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class); $kernel->bootstrap(); {php_code}"
    
    if USE_SSH:
        cmd = f'ssh -p 65002 -o StrictHostKeyChecking=no -i /Users/anuarlv/.ssh/id_rsa u129237724@185.212.70.24 \'cd domains/keiyi.digital/laravel_app && php -r "{php_template}"\''
    else:
        cmd = f"cd /Users/anuarlv/gemini/keiyi.digital && php -r \"{php_template}\""

    try:
        result = subprocess.run(cmd, shell=True, capture_output=True, text=True, check=True)
        import re
        match = re.search(r'(\[.*\]|\{.*\})', result.stdout, re.DOTALL)
        if match:
            return json.loads(match.group(1))
        return []
    except Exception as e:
        print(f"❌ Error al obtener fuentes vía Shell: {e}")
        return []


def extract_rss_headlines(url):
    """Extrae titulares de feeds RSS o Sitemap XML."""
    try:
        res = requests.get(url, headers=BROWSER_HEADERS, timeout=10)
        res.raise_for_status()
        root = ET.fromstring(res.content)
        headlines = []
        for item in root.findall('.//item')[:15]:
            title = item.find('title')
            title_text = title.text if title is not None else ''
            if title_text:
                headlines.append(title_text)
        return "\n".join(headlines)
    except Exception as e:
        print(f"⚠️  Error al leer RSS {url}: {e}")
        return ""


def extract_web_content(url, name):
    """
    Deep scraper para páginas HTML (currículas, temarios, páginas de cursos).
    Extrae contenido sustancioso ignorando navegación, footers y scripts.
    Limitado a 4000 chars para no saturar el contexto de Ollama.
    """
    if not BS4_AVAILABLE:
        print(f"   ⏭️  Omitiendo '{name}' — beautifulsoup4 no disponible.")
        return ""

    try:
        res = requests.get(url, headers=BROWSER_HEADERS, timeout=20)
        res.raise_for_status()

        soup = BeautifulSoup(res.text, 'html.parser')

        # Eliminar ruido: todo lo que no es contenido editorial
        for tag in soup(['script', 'style', 'nav', 'footer', 'aside',
                         'header', 'iframe', 'noscript', 'form', 'button']):
            tag.decompose()

        # Buscar el contenedor de contenido principal en orden de prioridad
        main_content = (
            soup.find('main') or
            soup.find('article') or
            soup.find(attrs={'id': lambda v: v and any(k in v.lower() for k in ['content', 'main', 'course', 'curriculum'])}) or
            soup.find(attrs={'class': lambda v: v and any(k in ' '.join(v).lower() for k in ['course-content', 'curriculum', 'syllabus', 'program', 'overview'])}) or
            soup.find('body')
        )

        if not main_content:
            return ""

        # Extraer texto de tags significativos, filtrando ruido corto
        parts = []
        for tag in main_content.find_all(['h1', 'h2', 'h3', 'h4', 'p', 'li']):
            text = tag.get_text(separator=' ', strip=True)
            if len(text) > 25:  # ignorar labels, botones, textos de relleno
                parts.append(text)

        raw_text = "\n".join(parts)
        return raw_text[:4000]

    except Exception as e:
        print(f"⚠️  Error al escarbar web {url}: {e}")
        return ""


def ask_ollama(context_text):
    """
    Envía el contexto acumulado a Ollama (qwen3:8b) y retorna el JSON de insights.
    El Prompt Maestro es ahora inyectado y manipulado dinámicamente leyendo
    el archivo 'prompt.txt' administrado por el Command Center del Jefe Supremo.
    """
    print(f"🧠 Consultando a Ollama (qwen3:8b) con {len(context_text)} caracteres...")

    try:
        # 1. Leer el Cerebro Dinámico (Caja Transparente)
        prompt_path = os.path.join(os.path.dirname(__file__), 'prompt.txt')
        if not os.path.exists(prompt_path):
            print("❌ Archivo de Prompt Maestro no encontrado. Genera uno desde el Command Center.")
            return None
            
        with open(prompt_path, 'r', encoding='utf-8') as file:
            base_prompt = file.read()
            
        # 2. Inyección de Contexto Raspeado
        if '{context_text}' not in base_prompt:
            print("⚠️  ADVERTENCIA: El prompt.txt no contiene {context_text}. Ollama no recibirá la data raspada.")
        prompt = base_prompt.replace('{context_text}', context_text)

    except Exception as e:
        print(f"❌ Error al cargar el Cerebro (prompt.txt): {e}")
        return None

    payload = {
        "model": "qwen3:8b",
        "prompt": prompt,
        "format": "json",
        "stream": False,
    }

    try:
        response = requests.post(OLLAMA_API_URL, json=payload, timeout=120)
        response.raise_for_status()
        data = response.json()
        ai_response_text = data.get("response", "{}")
        try:
            return json.loads(ai_response_text)
        except json.JSONDecodeError:
            print("❌ Ollama no entregó un JSON limpio. Abortando sync.")
            print(f"   Respuesta cruda: {ai_response_text[:300]}")
            return None
    except Exception as e:
        print(f"❌ Falla de comunicación con Ollama: {e}")
        return None


def sync_report_to_hostinger(json_report, source_count, scrape_status="Completado"):
    """Sube el reporte de Ollama al BD de Hostinger vía SCP y SSH."""
    if not json_report:
        return

    print("🚀 Empujando el Cerebro al Servidor vía SCP / Shell Directa...")

    json_report["scrape_status"] = scrape_status
    status_icon = "⚠️ Parcial" if scrape_status == "Parcial" else "🚨 Fallido" if scrape_status == "Fallido" else "✅ Integral"

    if "raw_sources_used" not in json_report:
        json_report["raw_sources_used"] = f"Análisis M2: {source_count} fuentes intentadas. Estado: {status_icon}"

    # Guardar a disco local en la M2
    filepath = "/tmp/keiyi_insight.json"
    with open(filepath, "w", encoding="utf-8") as f:
        json.dump(json_report, f, ensure_ascii=False)

    try:
        if USE_SSH:
            # Transferencia de archivo segura a Hostinger
            scp_cmd = "scp -P 65002 -i /Users/anuarlv/.ssh/id_rsa /tmp/keiyi_insight.json u129237724@185.212.70.24:/home/u129237724/domains/keiyi.digital/laravel_app/storage/app/keiyi_insight.json"
            subprocess.run(scp_cmd, shell=True, check=True, capture_output=True)
            
            # Ejecución de importación vía SSH
            php_code = r"$data = json_decode(file_get_contents(storage_path('app/keiyi_insight.json')), true); \App\Models\ScoutInsight::create(['report_date' => now()->toDateString(), 'detected_trends' => $data['detected_trends'], 'recommended_actions' => $data['recommended_actions'], 'raw_sources_used' => $data['raw_sources_used']]); echo json_encode(['success'=>true]);"
            php_template = f"require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class); $kernel->bootstrap(); {php_code}"
            
            ssh_cmd = f'ssh -p 65002 -o StrictHostKeyChecking=no -i /Users/anuarlv/.ssh/id_rsa u129237724@185.212.70.24 \'cd domains/keiyi.digital/laravel_app && php -r "{php_template}"\''
            subprocess.run(ssh_cmd, shell=True, check=True, capture_output=True)
        else:
            # Emulación de SCP local direct al PHP crudo
            php_code = r"$data = json_decode(file_get_contents('/tmp/keiyi_insight.json'), true); \App\Models\ScoutInsight::create(['report_date' => now()->toDateString(), 'detected_trends' => $data['detected_trends'], 'recommended_actions' => $data['recommended_actions'], 'raw_sources_used' => $data['raw_sources_used']]); echo json_encode(['success'=>true]);"
            php_template = f"require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class); $kernel->bootstrap(); {php_code}"
            
            local_cmd = f"cd /Users/anuarlv/gemini/keiyi.digital && php -r \"{php_template}\""
            subprocess.run(local_cmd, shell=True, check=True, capture_output=True)

        print("✅ ¡Sincronización SCP Exitosa! Ya disponible en la Base de Datos Keiyi.")
    except Exception as e:
        print(f"❌ Falló el posteo vía Shell Fuerte: {e}")

def main():
    print("=" * 55)
    print(f"🤖 AGENTE KEIYI SCOUT — Iniciando ({datetime.now().strftime('%Y-%m-%d %H:%M')})")
    print("=" * 55)

    sources = get_pending_sources()
    if not sources:
        print("📭 Sin fuentes en Hostinger. Activando antenas predeterminadas (Reddit + Universidades USA)...")
        sources = DEFAULT_SOURCES

    print(f"📋 {len(sources)} antenas configuradas. Iniciando barrido...\n")

    super_context = ""
    successful_count = 0
    failed_count = 0
    
    for idx, source in enumerate(sources):
        url     = source.get('url')
        name    = source.get('name')
        type_s  = source.get('type')

        print(f"   [{idx+1}/{len(sources)}] {name} ({type_s})")
        
        text_found = False

        if type_s in ['rss', 'sitemap']:
            text = extract_rss_headlines(url)
            if text:
                super_context += f"\n--- Titulares de {name} ---\n{text}\n"
                text_found = True

        elif type_s == 'web':
            text = extract_web_content(url, name)
            if text:
                super_context += f"\n--- Contenido Académico de {name} ---\n{text}\n"
                text_found = True

        else:
            print(f"   ⏭️  Tipo '{type_s}' no soportado aún. Omitiendo.")
            # Considerado 'exitoso' en término de error handling para no contar como fallo
            text_found = True

        if text_found:
            successful_count += 1
        else:
            failed_count += 1

    health_status = "Completado"
    if failed_count > 0:
        health_status = "Parcial"
    if successful_count == 0 and len(sources) > 0:
        health_status = "Fallido"

    if len(super_context.strip()) < 50:
        print("\n⚠️  Contexto insuficiente para análisis. Fin.")
        return

    final_insight_json = ask_ollama(super_context)
    sync_report_to_hostinger(final_insight_json, len(sources), health_status)
    print("\n🤖 Operación de Agente Finalizada.")


if __name__ == "__main__":
    main()
