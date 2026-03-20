#!/bin/bash
# Keiyi Idle Agent — Instalador
# Uso: bash agent/install_agent.sh

set -e

AGENT_DIR="$(cd "$(dirname "$0")" && pwd)"
LOG_DIR="$HOME/Library/Logs"
PLIST_NAME="digital.keiyi.idle_agent.plist"
LAUNCH_AGENTS="$HOME/Library/LaunchAgents"

echo "=== Keiyi Idle Agent — Instalador ==="
echo "Directorio del agente: $AGENT_DIR"

# 1. Instalar dependencias Python
echo ""
echo "→ Instalando dependencias Python..."
pip3 install rumps psutil --quiet
echo "  ✓ rumps y psutil instalados"

# 2. Generar el .plist con los paths reales
echo ""
echo "→ Configurando plist..."
sed \
  -e "s|AGENT_PATH_PLACEHOLDER|$AGENT_DIR|g" \
  -e "s|LOG_PATH_PLACEHOLDER|$LOG_DIR|g" \
  "$AGENT_DIR/$PLIST_NAME" > "$LAUNCH_AGENTS/$PLIST_NAME"
echo "  ✓ Plist instalado en $LAUNCH_AGENTS/$PLIST_NAME"

# 3. Descargar versión anterior si existe
launchctl unload "$LAUNCH_AGENTS/$PLIST_NAME" 2>/dev/null || true

# 4. Cargar el agente
launchctl load "$LAUNCH_AGENTS/$PLIST_NAME"
echo "  ✓ Agente cargado en launchd"

echo ""
echo "=== Instalación completa ==="
echo "El ícono 🤖 debería aparecer en tu menu bar en unos segundos."
echo ""
echo "Comandos útiles:"
echo "  Pausar:    launchctl unload ~/Library/LaunchAgents/$PLIST_NAME"
echo "  Reanudar:  launchctl load   ~/Library/LaunchAgents/$PLIST_NAME"
echo "  Ver logs:  tail -f ~/Library/Logs/keiyi_idle_agent.log"
echo "  Desinstalar: bash $AGENT_DIR/uninstall_agent.sh"
