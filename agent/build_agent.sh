#!/bin/bash
# Keiyi Agent — Build + Install (Swift nativo)
set -e

AGENT_DIR="$(cd "$(dirname "$0")" && pwd)"
APP_NAME="KeiyiAgent"
APP_BUNDLE="$AGENT_DIR/$APP_NAME.app"
BINARY="$APP_BUNDLE/Contents/MacOS/$APP_NAME"
PLIST_NAME="digital.keiyi.idle_agent.plist"
LAUNCH_AGENTS="$HOME/Library/LaunchAgents"

echo "=== Keiyi Agent — Build Nativo Swift ==="

# 1. Compilar
echo "→ Compilando Swift..."
swiftc "$AGENT_DIR/KeiyiAgent.swift" \
    -framework AppKit \
    -framework Foundation \
    -o "/tmp/$APP_NAME" \
    -O
echo "  ✓ Compilado"

# 2. Crear .app bundle
echo "→ Creando .app bundle..."
rm -rf "$APP_BUNDLE"
mkdir -p "$APP_BUNDLE/Contents/MacOS"
mv "/tmp/$APP_NAME" "$BINARY"
chmod +x "$BINARY"

cat > "$APP_BUNDLE/Contents/Info.plist" << PLIST
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>CFBundleExecutable</key>
    <string>$APP_NAME</string>
    <key>CFBundleIdentifier</key>
    <string>digital.keiyi.agent</string>
    <key>CFBundleName</key>
    <string>Keiyi Agent</string>
    <key>CFBundleVersion</key>
    <string>1.0</string>
    <key>LSUIElement</key>
    <true/>
    <key>NSSupportsAutomaticTermination</key>
    <false/>
    <key>LSMinimumSystemVersion</key>
    <string>14.0</string>
</dict>
</plist>
PLIST

echo "  ✓ Bundle creado en $APP_BUNDLE"

# 3. Instalar launchd plist
echo "→ Instalando en launchd..."
launchctl unload "$LAUNCH_AGENTS/$PLIST_NAME" 2>/dev/null || true

cat > "$LAUNCH_AGENTS/$PLIST_NAME" << LPLIST
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>digital.keiyi.idle_agent</string>
    <key>ProgramArguments</key>
    <array>
        <string>$BINARY</string>
    </array>
    <key>RunAtLoad</key>
    <true/>
    <key>KeepAlive</key>
    <true/>
    <key>ThrottleInterval</key>
    <integer>30</integer>
    <key>StandardOutPath</key>
    <string>$HOME/Library/Logs/keiyi_idle_agent_stdout.log</string>
    <key>StandardErrorPath</key>
    <string>$HOME/Library/Logs/keiyi_idle_agent_stderr.log</string>
    <key>WorkingDirectory</key>
    <string>$AGENT_DIR</string>
    <key>EnvironmentVariables</key>
    <dict>
        <key>PATH</key>
        <string>/usr/local/bin:/usr/bin:/bin:/opt/homebrew/bin:/Users/anuarlv/.claude/local</string>
    </dict>
</dict>
</plist>
LPLIST

launchctl load "$LAUNCH_AGENTS/$PLIST_NAME"
echo "  ✓ Agente cargado en launchd"

echo ""
echo "=== Instalación completa ==="
echo "El ícono 🤖 aparece en tu menu bar en segundos."
echo ""
echo "Comandos:"
echo "  Pausar:     launchctl unload ~/Library/LaunchAgents/$PLIST_NAME"
echo "  Reanudar:   launchctl load   ~/Library/LaunchAgents/$PLIST_NAME"
echo "  Logs:       tail -f ~/Library/Logs/keiyi_idle_agent.log"
echo "  Rebuild:    bash $AGENT_DIR/build_agent.sh"
