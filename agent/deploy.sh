#!/bin/bash
# =============================================================================
# DEPLOY — Sincroniza keiyi.digital a Hostinger de forma segura
# Uso: bash agent/deploy.sh
# =============================================================================
set -e

SSH="ssh -p 65002 -i $HOME/.ssh/id_rsa"
HOST="u129237724@185.212.70.24"
REMOTE="domains/keiyi.digital/laravel_app"
LOCAL="$(cd "$(dirname "$0")/.." && pwd)"

echo "=== DEPLOY KEIYI.DIGITAL ==="
echo "Local:  $LOCAL"
echo "Remote: $HOST:$REMOTE"
echo ""

# 0. Safety: only deploy from main branch
CURRENT_BRANCH=$(git -C "$LOCAL" branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo "ERROR: Solo se puede deployar desde la branch 'main'."
    echo "Estás en: $CURRENT_BRANCH"
    echo "Haz merge a main primero: git checkout main && git merge $CURRENT_BRANCH"
    exit 1
fi

# 1. Pre-check: site is currently up?
echo "→ Pre-check..."
HTTP_PRE=$(curl -so /dev/null -w '%{http_code}' --max-time 10 https://keiyi.digital 2>/dev/null)
echo "  Sitio actual: HTTP $HTTP_PRE"

# 2. Sync Laravel app (EXCLUDE index.php, .env, vendor, agent, local-only)
echo "→ Sincronizando código..."
rsync -avz --delete \
  --exclude='.env' \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='.git/' \
  --exclude='agent/' \
  --exclude='command-center/' \
  --exclude='papers/' \
  --exclude='.claude/' \
  --exclude='.DS_Store' \
  --exclude='.venv/' \
  --exclude='database/database.sqlite' \
  --exclude='public/' \
  -e "$SSH" \
  "$LOCAL/" \
  "$HOST:$REMOTE/" | tail -5

# 3. Sync public assets (EXCLUDE index.php and .htaccess — NEVER overwrite these)
echo "→ Sincronizando assets públicos..."
rsync -avz \
  --exclude='index.php' \
  --exclude='.htaccess' \
  --exclude='storage' \
  -e "$SSH" \
  "$LOCAL/public/" \
  "$HOST:~/domains/keiyi.digital/public_html/" | tail -5

# 4. Composer install + migrate + clear cache
echo "→ Instalando dependencias..."
$SSH $HOST "cd $REMOTE && php composer.phar install --no-dev --optimize-autoloader --quiet 2>&1" | tail -3

echo "→ Migraciones..."
$SSH $HOST "cd $REMOTE && php artisan migrate --force 2>&1"

echo "→ Limpiando caché..."
$SSH $HOST "cd $REMOTE && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear 2>&1" | tail -4

# 5. Post-check: site is still up?
echo "→ Post-check..."
sleep 2
HTTP_POST=$(curl -so /dev/null -w '%{http_code}' --max-time 10 https://keiyi.digital 2>/dev/null)
echo "  Sitio después del deploy: HTTP $HTTP_POST"

if [ "$HTTP_POST" = "200" ]; then
    echo ""
    echo "=== DEPLOY EXITOSO ==="
else
    echo ""
    echo "=== ALERTA: EL SITIO DEVOLVIÓ HTTP $HTTP_POST ==="
    echo "Revisa los logs: ssh -p 65002 u129237724@185.212.70.24 'cd $REMOTE && tail -20 storage/logs/laravel.log'"
fi
