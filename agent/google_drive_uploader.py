#!/usr/bin/env python3
# Keiyi Google Drive Uploader — Local Sync (sin API)
# Escribe los reportes directamente a la carpeta de Google Drive Desktop.
# Google Drive para escritorio los sincroniza automáticamente a la nube.

import os
import json
from datetime import datetime

AGENT_DIR   = os.path.dirname(__file__)
RESEARCH_DB_PATH = os.path.join(AGENT_DIR, 'research_db.json')

# Carpeta local de Google Drive — ya creada y sincronizada
DRIVE_FOLDER = "/Users/anuarlv/Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/Keiyi Scout Intelligence"

def build_intelligence_report(db):
    """Formatea el research_db como texto legible para NotebookLM."""
    lines = []
    ts = db.get('last_updated', 'N/A')
    lines.append("# Keiyi Scout Intelligence Report")
    lines.append(f"Generado: {ts}\n")

    lines.append("## Top Herramientas Mencionadas en Reddit\n")
    tools = sorted(db.get('tools', {}).values(), key=lambda x: x['count'], reverse=True)[:30]
    for i, t in enumerate(tools, 1):
        sources = ', '.join(t.get('sources', []))
        lines.append(f"{i}. **{t['display']}** — {t['count']} menciones ({sources})")

    lines.append("\n## Preguntas Más Frecuentes\n")
    questions = sorted(db.get('questions', {}).values(), key=lambda x: x['count'], reverse=True)[:20]
    for i, q in enumerate(questions, 1):
        sources = ', '.join(q.get('sources', []))
        lines.append(f"{i}. {q['display']} [{q['count']}x — {sources}]")

    lines.append("\n## Referencias y Recursos Más Citados\n")
    refs = sorted(db.get('references', {}).values(), key=lambda x: x['count'], reverse=True)[:20]
    for i, r in enumerate(refs, 1):
        sources = ', '.join(r.get('sources', []))
        lines.append(f"{i}. {r['display']} [{r['count']}x — {sources}]")

    return "\n".join(lines)

def upload_to_drive():
    """Escribe los reportes a la carpeta local de Google Drive Desktop."""
    print("\n☁️  Sincronizando inteligencia con Google Drive...")

    if not os.path.exists(DRIVE_FOLDER):
        print(f"❌ Carpeta no encontrada: {DRIVE_FOLDER}")
        print("   Verifica que Google Drive Desktop esté corriendo.")
        return False

    if not os.path.exists(RESEARCH_DB_PATH):
        print("❌ No hay research_db.json aún. Ejecuta primero el Análisis Profundo.")
        return False

    try:
        with open(RESEARCH_DB_PATH, 'r', encoding='utf-8') as f:
            db = json.load(f)

        timestamp = datetime.now().strftime('%Y-%m-%d')

        # 1. Reporte legible para NotebookLM
        report_path = os.path.join(DRIVE_FOLDER, f'intel_report_{timestamp}.txt')
        with open(report_path, 'w', encoding='utf-8') as f:
            f.write(build_intelligence_report(db))
        print(f"   ✅ intel_report_{timestamp}.txt")

        # 2. JSON crudo (para procesamiento futuro)
        json_path = os.path.join(DRIVE_FOLDER, 'research_db_latest.json')
        with open(json_path, 'w', encoding='utf-8') as f:
            json.dump(db, f, ensure_ascii=False, indent=2)
        print(f"   ✅ research_db_latest.json")

        print(f"   📁 {DRIVE_FOLDER}")
        print("   🔄 Google Drive Desktop sincronizará en segundos.")
        return True

    except Exception as e:
        print(f"❌ Error escribiendo a Drive: {e}")
        return False

if __name__ == "__main__":
    upload_to_drive()
