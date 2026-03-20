#!/usr/bin/env python3
import os
import requests
import json
import xml.etree.ElementTree as ET
from datetime import datetime

# ==============================================================================
# DIPPER (SCOUT AI) — 100% LOCAL-FIRST
# ==============================================================================

OLLAMA_API_URL = "http://localhost:11434/api/generate"
MODEL = "deepseek-r1:7b" # Modelo para razonamiento lógico
INSIGHTS_DIR = os.path.join(os.path.dirname(__file__), 'insights')

os.makedirs(INSIGHTS_DIR, exist_ok=True)

# Solo fuentes de Reddit por ahora (las más estables)
SOURCES = [
    {"name": "r/edtech", "url": "https://www.reddit.com/r/edtech/.rss", "type": "rss"},
    {"name": "r/artificial", "url": "https://www.reddit.com/r/artificial/.rss", "type": "rss"},
    {"name": "r/digital_marketing", "url": "https://www.reddit.com/r/digital_marketing/.rss", "type": "rss"},
    {"name": "r/marketing", "url": "https://www.reddit.com/r/marketing/.rss", "type": "rss"},
]

HEADERS = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)"}

def extract_rss(url):
    try:
        res = requests.get(url, headers=HEADERS, timeout=10)
        root = ET.fromstring(res.content)
        return [{"title": i.find('title').text, "link": i.find('link').text} for i in root.findall('.//item')[:10]]
    except: return []

def ask_ollama(context):
    print(f"🧠 Dipper analizando inteligencia localmente con {MODEL}...")
    prompt = f"Analiza estos temas y extrae 3 tendencias clave. Entrega un JSON con 'detected_trends' (lista de objetos con name, summary, link_referencia) y 'recommended_actions' (lista de 3 acciones).\nDATA:\n{context}"
    payload = {"model": MODEL, "prompt": prompt, "format": "json", "stream": False}
    try:
        r = requests.post(OLLAMA_API_URL, json=payload, timeout=120)
        return json.loads(r.json().get("response", "{}"))
    except Exception as e:
        print(f"❌ Error Ollama: {e}")
        return None

def main():
    print("🤖 Dipper Iniciando Barrido...")
    full_context = ""
    for s in SOURCES:
        data = extract_rss(s['url'])
        if data:
            full_context += f"\n--- {s['name']} ---\n"
            for item in data: full_context += f"- {item['title']} (URL: {item['link']})\n"
    
    if full_context:
        insight = ask_ollama(full_context)
        if insight:
            filename = f"insight_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
            with open(os.path.join(INSIGHTS_DIR, filename), 'w', encoding='utf-8') as f:
                json.dump(insight, f, ensure_ascii=False, indent=4)
            print(f"✅ Dipper terminó. Reporte listo en: {filename}")

if __name__ == "__main__":
    main()
