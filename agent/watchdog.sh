#!/bin/bash
# =============================================================================
# WATCHDOG — Revisión diaria de salud de keiyi.digital
# Corre 1 vez al día a las 5 AM via cron.
# Revisa que el sitio y sus links principales estén funcionando.
# =============================================================================

SITE_URL="https://keiyi.digital"
LOG_FILE="$HOME/Library/Logs/keiyi_watchdog.log"
STATUS_FILE="$(dirname "$0")/watchdog_status.json"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

# Links a verificar
LINKS=(
    "/"
    "/blog"
    "/academy"
    "/login"
    "/admin"
)

echo "[$TIMESTAMP] === WATCHDOG CHECK ===" >> "$LOG_FILE"

ALL_OK=true
RESULTS=""

for path in "${LINKS[@]}"; do
    HTTP_CODE=$(curl -so /dev/null -w '%{http_code}' --max-time 15 "${SITE_URL}${path}" 2>/dev/null)

    if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
        RESULTS="${RESULTS}  ✓ ${path} → ${HTTP_CODE}\n"
    else
        RESULTS="${RESULTS}  ✗ ${path} → ${HTTP_CODE}\n"
        ALL_OK=false
    fi
done

echo -e "$RESULTS" >> "$LOG_FILE"

if [ "$ALL_OK" = true ]; then
    STATE="up"
    echo "[$TIMESTAMP] ALL OK" >> "$LOG_FILE"
else
    STATE="issues"
    echo "[$TIMESTAMP] ISSUES DETECTED" >> "$LOG_FILE"

    # Notificar
    osascript -e "display notification \"keiyi.digital tiene problemas. Revisa el log.\" with title \"WATCHDOG\" sound name \"Basso\"" 2>/dev/null

    # Diagnóstico via SSH
    DIAG=$(ssh -p 65002 -i "$HOME/.ssh/id_rsa" -o ConnectTimeout=10 -o StrictHostKeyChecking=no \
        u129237724@185.212.70.24 \
        "cd domains/keiyi.digital/laravel_app && tail -5 storage/logs/laravel.log 2>/dev/null" 2>/dev/null)
    if [ -n "$DIAG" ]; then
        echo "[$TIMESTAMP] DIAG: $DIAG" >> "$LOG_FILE"
    fi
fi

# Guardar estado
cat > "$STATUS_FILE" << EOF
{
  "state": "$STATE",
  "checked_at": "$TIMESTAMP",
  "url": "$SITE_URL",
  "links_checked": ${#LINKS[@]}
}
EOF
