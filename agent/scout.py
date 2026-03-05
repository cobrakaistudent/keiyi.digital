#!/usr/bin/env python3
import os
import requests
import json
import xml.etree.ElementTree as ET
from datetime import datetime

# ==============================================================================
# KEIYI SCOUT AI - CEREBRO LOCAL (Mac M2)
# ==============================================================================
# Propósito: Ejecutar carga pesada de Análisis AI mediante Ollama local para
# ahorrar recursos de servidor en Hostinger y asegurar máxima privacidad de datos.
# ==============================================================================

LARAVEL_API_BASE = "http://127.0.0.1:8000/api"
OLLAMA_API_URL = "http://localhost:11434/api/generate"
# Leer token blindado desde node env
SANCTUM_TOKEN = os.environ.get("SANCTUM_TOKEN", "your_sanctum_token_here")

def get_pending_sources():
    """Descarga la agenda de vigilancia desde el servidor web Keiyi"""
    print("📡 Conectando a Keiyi Web API...")
    headers = {"Authorization": f"Bearer {SANCTUM_TOKEN}", "Accept": "application/json"}
    try:
        response = requests.get(f"{LARAVEL_API_BASE}/scout/pending", headers=headers)
        response.raise_for_status()
        data = response.json()
        if data.get("status") == "success":
            return data.get("data", [])
        return []
    except Exception as e:
        print(f"❌ Error al obtener fuentes: {e}")
        return []

def extract_rss_headlines(url):
    """Extrae texto plano ligero y amigable para el modelo Gemma3"""
    try:
        res = requests.get(url, timeout=10)
        res.raise_for_status()
        # Parse XML
        root = ET.fromstring(res.content)
        headlines = []
        for item in root.findall('.//item')[:15]: # Limite 15 noticias por fuente
            title = item.find('title')
            title_text = title.text if title is not None else ''
            if title_text:
                headlines.append(title_text)
        return "\n".join(headlines)
    except Exception as e:
        print(f"⚠️ Error al leer RSS {url}: {e}")
        return ""

def ask_ollama(context_text):
    """Pide al cerebro Mac M2 que genere Megatendencias EdTech en JSON rígido"""
    print(f"🧠 Consultando a Ollama (qwen3:8b) con {len(context_text)} caracteres...")
    
    prompt = f"""
Eres Keiyi Scout, un analista experto en modelos de negocio EdTech y tendencias de marketing.
Lee rigurosamente el siguiente compendio de noticias de las últimas 24 horas:

{context_text}

Tu misión:
Identificar las 3 principales megatendencias educativas de hoy y devolver 3 acciones recomendadas para la Agencia Keiyi.

REGLA ABSOLUTA DE SALIDA:
Devuelve ÚNICA Y EXCLUSIVAMENTE un JSON puro con esta estructura, sin tildes ni caracteres markdown de bloque:
{{
  "detected_trends": ["Tendencia 1", "Tendencia 2", "Tendencia 3"],
  "recommended_actions": ["Sugerencia 1", "Sugerencia 2", "Sugerencia 3"],
  "raw_sources_used": "#KeiyiScout #AI #Educacion"
}}
"""
    
    payload = {
        "model": "qwen3:8b",
        "prompt": prompt,
        "format": "json", # Forzar modo JSON en Ollama
        "stream": False
    }
    
    try:
        response = requests.post(OLLAMA_API_URL, json=payload, timeout=120) # 2 mins timeout max
        response.raise_for_status()
        data = response.json()
        ai_response_text = data.get("response", "{}")
        # Ensayar parseo seguro
        try:
            json_report = json.loads(ai_response_text)
            return json_report
        except json.JSONDecodeError:
             print("❌ Ollama falló al entregar un JSON limpio. Abortando sync.")
             print(f"Respuesta Cruda: {ai_response_text}")
             return None
             
    except Exception as e:
        print(f"❌ Falla de comunicación con Ollama: {e}")
        return None

def sync_report_to_hostinger(json_report, source_count):
    """Sube los resultados mágicos al panel Administrativo CRM del jefe"""
    if not json_report:
        return
        
    print("🚀 Empujando el Cerebro al Servidor Hostinger...")
    headers = {"Authorization": f"Bearer {SANCTUM_TOKEN}", "Accept": "application/json"}
    
    # Aseguramos inyeccion de hashtags adicionales para la BD if missing
    if "raw_sources_used" not in json_report:
        json_report["raw_sources_used"] = f"Análisis M2: {source_count} Fuentes de mercado."
        
    try:
        post_response = requests.post(
            f"{LARAVEL_API_BASE}/scout/insight", 
            json=json_report, 
            headers=headers
        )
        post_response.raise_for_status()
        print("✅ ¡Sincronización Exitosa! Ya disponible en el CRM Keiyi.")
    except Exception as e:
        print(f"❌ Falló el posteo al Servidor: {e}")

def main():
    print("=" * 50)
    print(f"🤖 AGENTE KEIYI SCOUT - Iniciando ({datetime.now()})")
    print("=" * 50)
    
    sources = get_pending_sources()
    if not sources:
        print("📭 No hay fuentes configuradas en el Dashboard. Descansando.")
        return
        
    print(f"📋 Encontradas {len(sources)} antenas configuradas por la Jefatura.")
    
    super_context = ""
    for idx, source in enumerate(sources):
        url = source.get('url')
        name = source.get('name')
        type_s = source.get('type')
        
        print(f"   [{idx+1}/{len(sources)}] Barrido de: {name} ({type_s})")
        if type_s in ['rss', 'sitemap']:
            text = extract_rss_headlines(url)
            if text:
                super_context += f"\n--- Titulares de {name} ---\n{text}\n"
    
    if len(super_context.strip()) < 50:
        print("⚠️ No hay suficiente carnita (texto informativo) hoy para molestar a la IA. Fin.")
        return
        
    # Magia Negra (AI Processing)
    final_insight_json = ask_ollama(super_context)
    
    # Sync de Nube a Tierra
    sync_report_to_hostinger(final_insight_json, len(sources))
    print("🤖 Operación de Agente Finalizada.")

if __name__ == "__main__":
    main()
