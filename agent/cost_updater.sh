#!/bin/bash
# =============================================================================
# COST UPDATER — Actualización diaria de costos y fórmulas de pricing
# Corre junto con el watchdog a las 5 AM via cron.
# =============================================================================

AGENT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(cd "$AGENT_DIR/.." && pwd)"
LOG_FILE="$HOME/Library/Logs/keiyi_cost_updater.log"
TIMESTAMP=$(date '+%Y-%m-%dT%H:%M:%S')

echo "[$TIMESTAMP] === COST UPDATER ===" >> "$LOG_FILE"

cd "$PROJECT_DIR" || exit 1

# 1. Actualizar tipo de cambio USD→MXN
USD_MXN=$(curl -s "https://open.er-api.com/v6/latest/USD" 2>/dev/null | python3 -c "import sys,json;print(json.load(sys.stdin)['rates']['MXN'])" 2>/dev/null)
if [ -n "$USD_MXN" ]; then
    echo "  TC USD→MXN: $USD_MXN" >> "$LOG_FILE"
    # Invalidar cache de Laravel para que tome el nuevo valor
    php artisan cache:forget usd_to_mxn 2>/dev/null
else
    echo "  ⚠ No se pudo obtener TC" >> "$LOG_FILE"
fi

# 2. Recalcular overhead mensual total desde BusinessCosts
OVERHEAD=$(php artisan tinker --execute="
\$total = \App\Models\BusinessCost::active()->get()->sum(function(\$c){ return \$c->monthly_cost; });
echo round(\$total, 2);
" 2>/dev/null | tail -1)

if [ -n "$OVERHEAD" ] && [ "$OVERHEAD" != "0" ]; then
    # Actualizar en PricingConfig
    php artisan tinker --execute="
    \$cfg = \App\Models\PricingConfig::where('key','monthly_overhead')->first();
    if(\$cfg) { \$cfg->update(['value' => '$OVERHEAD']); echo 'Overhead actualizado: \$$OVERHEAD MXN'; }
    " 2>/dev/null | tail -1 >> "$LOG_FILE"
else
    echo "  ⚠ No se pudo calcular overhead" >> "$LOG_FILE"
fi

# 3. Verificar inventario bajo de filamento
LOW_STOCK=$(php artisan tinker --execute="
\$low = \App\Models\FilamentInventory::where('status','active')->where('remaining_grams','<', 200)->count();
echo \$low;
" 2>/dev/null | tail -1)

if [ "$LOW_STOCK" -gt 0 ] 2>/dev/null; then
    echo "  ⚠ ALERTA: $LOW_STOCK filamentos con menos de 200g" >> "$LOG_FILE"
    osascript -e "display notification \"$LOW_STOCK filamentos con stock bajo (<200g)\" with title \"Keiyi 3D\" subtitle \"Revisar inventario\" sound name \"Glass\"" 2>/dev/null
fi

# 4. Log resumen de pricing actual
PRICING=$(php artisan tinker --execute="
\$r = \App\Models\PricingConfig::calculatePrintCost(100, 2);
echo 'Ref 100g/2h: \$'.number_format(\$r['final_price'],2).' MXN (margen '.\$r['margin_pct'].'%, demanda x'.\$r['demand_factor'].')';
" 2>/dev/null | tail -1)

echo "  $PRICING" >> "$LOG_FILE"
echo "[$TIMESTAMP] COST UPDATER OK" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
