#!/bin/bash
# Keiyi Idle Agent — Desinstalador

PLIST_NAME="digital.keiyi.idle_agent.plist"
LAUNCH_AGENTS="$HOME/Library/LaunchAgents"

echo "→ Deteniendo y removiendo Keiyi Idle Agent..."
launchctl unload "$LAUNCH_AGENTS/$PLIST_NAME" 2>/dev/null || true
rm -f "$LAUNCH_AGENTS/$PLIST_NAME"
echo "✓ Agente desinstalado. El ícono 🤖 desaparecerá del menu bar."
