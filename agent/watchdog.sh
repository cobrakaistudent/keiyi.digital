#!/bin/bash
# =============================================================================
# WATCHDOG — Revisión diaria de salud + métricas de keiyi.digital
# Corre 1 vez al día a las 5 AM via cron.
# Revisa que el sitio y sus links funcionen + captura métricas de rendimiento.
# =============================================================================

SITE_URL="https://keiyi.digital"
LOG_FILE="$HOME/Library/Logs/keiyi_watchdog.log"
METRICS_FILE="$(dirname "$0")/watchdog_metrics.json"
STATUS_FILE="$(dirname "$0")/watchdog_status.json"
TIMESTAMP=$(date '+%Y-%m-%dT%H:%M:%S')
DATE_SHORT=$(date '+%Y-%m-%d')

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
LINK_RESULTS="["
TOTAL_TIME=0
COUNT=0

for path in "${LINKS[@]}"; do
    # Capturar HTTP code, tiempo de respuesta (ms), y tamaño (bytes)
    RESPONSE=$(curl -so /dev/null \
        -w '%{http_code}|%{time_total}|%{size_download}|%{time_starttfb}' \
        --max-time 15 "${SITE_URL}${path}" 2>/dev/null)

    HTTP_CODE=$(echo "$RESPONSE" | cut -d'|' -f1)
    TIME_TOTAL=$(echo "$RESPONSE" | cut -d'|' -f2)
    SIZE_BYTES=$(echo "$RESPONSE" | cut -d'|' -f3)
    TTFB=$(echo "$RESPONSE" | cut -d'|' -f4)

    # Convertir a ms
    TIME_MS=$(python3 -c "print(int(float('${TIME_TOTAL}') * 1000))" 2>/dev/null || echo "0")
    TTFB_MS=$(python3 -c "print(int(float('${TTFB}') * 1000))" 2>/dev/null || echo "0")
    SIZE_KB=$(python3 -c "print(round(float('${SIZE_BYTES}') / 1024, 1))" 2>/dev/null || echo "0")

    TOTAL_TIME=$((TOTAL_TIME + TIME_MS))
    COUNT=$((COUNT + 1))

    OK=true
    if [ "$HTTP_CODE" != "200" ] && [ "$HTTP_CODE" != "302" ]; then
        ALL_OK=false
        OK=false
    fi

    # Log
    if [ "$OK" = true ]; then
        echo "  ✓ ${path} → ${HTTP_CODE} · ${TIME_MS}ms · ${SIZE_KB}KB · TTFB ${TTFB_MS}ms" >> "$LOG_FILE"
    else
        echo "  ✗ ${path} → ${HTTP_CODE} · ${TIME_MS}ms" >> "$LOG_FILE"
    fi

    # JSON para métricas
    [ "$COUNT" -gt 1 ] && LINK_RESULTS="${LINK_RESULTS},"
    LINK_RESULTS="${LINK_RESULTS}{\"path\":\"${path}\",\"http\":${HTTP_CODE},\"time_ms\":${TIME_MS},\"ttfb_ms\":${TTFB_MS},\"size_kb\":${SIZE_KB}}"
done

LINK_RESULTS="${LINK_RESULTS}]"
AVG_TIME=$((TOTAL_TIME / COUNT))

# SSL check — días hasta expiración
SSL_EXPIRY=$(echo | openssl s_client -servername keiyi.digital -connect keiyi.digital:443 2>/dev/null | openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2)
SSL_DAYS="null"
if [ -n "$SSL_EXPIRY" ]; then
    SSL_EPOCH=$(date -j -f "%b %d %T %Y %Z" "$SSL_EXPIRY" "+%s" 2>/dev/null || date -d "$SSL_EXPIRY" "+%s" 2>/dev/null)
    NOW_EPOCH=$(date "+%s")
    if [ -n "$SSL_EPOCH" ]; then
        SSL_DAYS=$(( (SSL_EPOCH - NOW_EPOCH) / 86400 ))
    fi
fi

echo "  AVG response: ${AVG_TIME}ms · SSL expires in: ${SSL_DAYS} days" >> "$LOG_FILE"

if [ "$ALL_OK" = true ]; then
    STATE="up"
    echo "[$TIMESTAMP] ALL OK" >> "$LOG_FILE"
else
    STATE="issues"
    echo "[$TIMESTAMP] ISSUES DETECTED" >> "$LOG_FILE"
    osascript -e "display notification \"keiyi.digital tiene problemas. Revisa el log.\" with title \"WATCHDOG\" sound name \"Basso\"" 2>/dev/null

    DIAG=$(ssh -p 65002 -i "$HOME/.ssh/id_rsa" -o ConnectTimeout=10 -o StrictHostKeyChecking=no \
        u129237724@185.212.70.24 \
        "cd domains/keiyi.digital/laravel_app && tail -5 storage/logs/laravel.log 2>/dev/null" 2>/dev/null)
    if [ -n "$DIAG" ]; then
        echo "[$TIMESTAMP] DIAG: $DIAG" >> "$LOG_FILE"
    fi
fi

# Guardar status actual
cat > "$STATUS_FILE" << EOF
{
  "state": "$STATE",
  "checked_at": "$TIMESTAMP",
  "url": "$SITE_URL",
  "links_checked": ${#LINKS[@]},
  "avg_response_ms": $AVG_TIME,
  "ssl_days_remaining": $SSL_DAYS
}
EOF

# Guardar métricas históricas (append al archivo JSON array)
# Formato: array de objetos, un registro por día
METRIC_ENTRY="{\"date\":\"$DATE_SHORT\",\"state\":\"$STATE\",\"avg_ms\":$AVG_TIME,\"ssl_days\":$SSL_DAYS,\"links\":$LINK_RESULTS}"

if [ -f "$METRICS_FILE" ]; then
    # Append al array existente (quitar ] final, agregar nueva entrada)
    python3 -c "
import json
with open('$METRICS_FILE') as f:
    data = json.load(f)
entry = json.loads('$METRIC_ENTRY')
# No duplicar mismo día
data = [d for d in data if d.get('date') != entry['date']]
data.append(entry)
# Mantener solo últimos 90 días
data = data[-90:]
with open('$METRICS_FILE', 'w') as f:
    json.dump(data, f, indent=2, ensure_ascii=False)
" 2>/dev/null
else
    echo "[$METRIC_ENTRY]" | python3 -c "import sys,json;print(json.dumps(json.load(sys.stdin),indent=2))" > "$METRICS_FILE" 2>/dev/null
fi

# ── Check for new William drafts pending review ──
AGENT_DIR="$(dirname "$0")"
DRAFTS_DIR="$AGENT_DIR/william_drafts"
DRAFTS_SEEN="$AGENT_DIR/watchdog_drafts_seen.txt"

if [ -d "$DRAFTS_DIR" ]; then
    touch "$DRAFTS_SEEN"
    NEW_DRAFTS=0
    for f in "$DRAFTS_DIR"/*.md; do
        [ -f "$f" ] || continue
        fname=$(basename "$f")
        if ! grep -q "$fname" "$DRAFTS_SEEN" 2>/dev/null; then
            NEW_DRAFTS=$((NEW_DRAFTS + 1))
            echo "$fname" >> "$DRAFTS_SEEN"
        fi
    done

    if [ "$NEW_DRAFTS" -gt 0 ]; then
        echo "  📝 $NEW_DRAFTS nuevos borradores de William pendientes de revisión" >> "$LOG_FILE"
        osascript -e "display notification \"$NEW_DRAFTS nuevos artículos por revisar en la mesa de edición\" with title \"Keiyi Blog\" subtitle \"William generó contenido nuevo\" sound name \"Glass\"" 2>/dev/null
    fi
fi

echo "" >> "$LOG_FILE"
