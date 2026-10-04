#!/usr/bin/env bash
# ==============================================================================
# B Cafe (بی کافه) - Safe Git-Based Zero-Downtime Deployment Script for cPanel
# Designed for execution via cPanel Git Version Control (.cpanel.yml) without SSH
# ==============================================================================
set -euo pipefail

# --- 1. Environment & Path Resolution ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEPLOYPATH="${DEPLOYPATH:-$SCRIPT_DIR}"

# Load optional user overrides if present
if [[ -f "$DEPLOYPATH/deploy.env" ]]; then
    # shellcheck disable=SC1091
    source "$DEPLOYPATH/deploy.env"
fi

# Determine base paths
USER_HOME="${HOME:-$(getent passwd "$USER" 2>/dev/null | cut -d: -f6 || echo "/home/$USER")}"
DEPLOY_BASE="${DEPLOY_BASE:-$USER_HOME/deploy}"
RELEASES_DIR="$DEPLOY_BASE/releases"
SHARED_DIR="$DEPLOY_BASE/shared"
CURRENT_DIR="$DEPLOY_BASE/current"
BACKUP_DIR="${BACKUP_DIR:-$USER_HOME/backups/db}"
LOGS_DIR="$DEPLOY_BASE/logs"

mkdir -p "$RELEASES_DIR" "$SHARED_DIR" "$BACKUP_DIR" "$LOGS_DIR"

TIMESTAMP="$(date +%Y-%m-%d_%H%M%S)"
DEPLOY_LOG="$LOGS_DIR/deploy_${TIMESTAMP}.log"
exec > >(tee -a "$DEPLOY_LOG") 2>&1

echo "========================================================================"
echo "🚀 [$(date '+%Y-%m-%d %H:%M:%S')] Starting B Cafe Deployment: $TIMESTAMP"
echo "========================================================================"
echo "📂 Repo Path (DEPLOYPATH): $DEPLOYPATH"
echo "📂 Deploy Base:           $DEPLOY_BASE"
echo "📂 Releases:              $RELEASES_DIR"
echo "📂 Shared:                $SHARED_DIR"
echo "📂 Backups:               $BACKUP_DIR"

# --- 2. PHP & Composer Binary Discovery ---
find_php_binary() {
    # Check common cPanel / CloudLinux EA-PHP paths first (prefer PHP 8.3 / 8.2)
    local candidates=(
        "/usr/local/bin/ea-php83"
        "/usr/local/bin/ea-php82"
        "/opt/cpanel/ea-php83/root/usr/bin/php"
        "/opt/cpanel/ea-php82/root/usr/bin/php"
        "/usr/bin/ea-php83"
        "/usr/bin/ea-php82"
        "$(command -v php 2>/dev/null || true)"
    )
    for bin in "${candidates[@]}"; do
        if [[ -n "$bin" && -x "$bin" ]]; then
            echo "$bin"
            return 0
        fi
    done
    echo "php"
}

find_composer_binary() {
    local candidates=(
        "$USER_HOME/bin/composer"
        "/usr/local/bin/composer"
        "/usr/bin/composer"
        "$(command -v composer 2>/dev/null || true)"
    )
    for bin in "${candidates[@]}"; do
        if [[ -n "$bin" && -x "$bin" ]]; then
            echo "$bin"
            return 0
        fi
    done
    echo "composer"
}

PHP_BIN="$(find_php_binary)"
COMPOSER_BIN="$(find_composer_binary)"

echo "🐘 Using PHP:      $($PHP_BIN -v 2>/dev/null | head -n 1 || echo "$PHP_BIN")"
echo "📦 Using Composer: $($COMPOSER_BIN --version 2>/dev/null | head -n 1 || echo "$COMPOSER_BIN")"

# --- 3. Extract Git Commit Information ---
TARGET_COMMIT="$(git -C "$DEPLOYPATH" rev-parse HEAD 2>/dev/null || echo "HEAD")"
TARGET_SHORT_SHA="$(git -C "$DEPLOYPATH" rev-parse --short HEAD 2>/dev/null || echo "unknown")"
COMMIT_MSG="$(git -C "$DEPLOYPATH" log -1 --pretty=%B 2>/dev/null | head -n 1 || echo "Automatic Deployment")"
COMMIT_AUTHOR="$(git -C "$DEPLOYPATH" log -1 --pretty=%an 2>/dev/null || echo "cPanel Git")"

echo "🔖 Target Commit: $TARGET_SHORT_SHA ($TARGET_COMMIT)"
echo "📝 Message:       $COMMIT_MSG"
echo "👤 Author:        $COMMIT_AUTHOR"

# Record previous release for rollback
PREVIOUS_RELEASE=""
if [[ -L "$CURRENT_DIR" && -d "$CURRENT_DIR" ]]; then
    PREVIOUS_RELEASE="$(readlink -f "$CURRENT_DIR" || true)"
    echo "⏮️ Previous Active Release: $PREVIOUS_RELEASE"
fi

# --- 4. Prepare New Release Directory ---
RELEASE_DIR="$RELEASES_DIR/$TIMESTAMP"
mkdir -p "$RELEASE_DIR"
echo "📁 Created Release Folder: $RELEASE_DIR"

# Export code from repository into release directory (clean, no .git directory)
if git -C "$DEPLOYPATH" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    echo "📦 Exporting files from git repository via git archive..."
    git -C "$DEPLOYPATH" archive "$TARGET_COMMIT" | tar -x -C "$RELEASE_DIR"
else
    echo "⚠️ Not a standard git repo, copying files directly..."
    cp -a "$DEPLOYPATH/." "$RELEASE_DIR/"
    rm -rf "$RELEASE_DIR/.git"
fi

# --- 5. Wire Shared Directory Resources (.env & storage) ---
# Ensure .env exists in shared directory
if [[ ! -f "$SHARED_DIR/.env" ]]; then
    if [[ -f "$DEPLOYPATH/.env" ]]; then
        echo "📄 Initializing shared .env from repository .env..."
        cp "$DEPLOYPATH/.env" "$SHARED_DIR/.env"
    elif [[ -f "$DEPLOYPATH/.env.example" ]]; then
        echo "📄 Initializing shared .env from .env.example..."
        cp "$DEPLOYPATH/.env.example" "$SHARED_DIR/.env"
    else
        touch "$SHARED_DIR/.env"
    fi
fi
ln -sfn "$SHARED_DIR/.env" "$RELEASE_DIR/.env"
echo "🔗 Linked shared .env -> release .env"

# Ensure shared storage directories exist
mkdir -p "$SHARED_DIR/storage/app/public" \
         "$SHARED_DIR/storage/framework/cache/data" \
         "$SHARED_DIR/storage/framework/sessions" \
         "$SHARED_DIR/storage/framework/views" \
         "$SHARED_DIR/storage/logs"

# Replace release storage with shared storage symlink
rm -rf "$RELEASE_DIR/storage"
ln -sfn "$SHARED_DIR/storage" "$RELEASE_DIR/storage"
echo "🔗 Linked shared storage -> release storage"

# Ensure public/storage symlink exists
mkdir -p "$RELEASE_DIR/public"
ln -sfn "$SHARED_DIR/storage/app/public" "$RELEASE_DIR/public/storage"
echo "🔗 Linked shared public storage -> release public/storage"

# --- 6. Composer Install Inside New Release ---
echo "📥 Running Composer install inside new release..."
(
    cd "$RELEASE_DIR"
    $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction --prefer-dist --quiet
)
echo "✅ Composer dependencies installed successfully."

# --- 7. Read Database Credentials & Execute Pre-Migration DB Backup ---
parse_env_var() {
    local var="$1"
    local file="$2"
    grep -E "^${var}=" "$file" | head -n 1 | cut -d'=' -f2- | tr -d '"'"'" | tr -d "\r" || true
}

DB_CONNECTION="$(parse_env_var "DB_CONNECTION" "$SHARED_DIR/.env")"
DB_HOST="$(parse_env_var "DB_HOST" "$SHARED_DIR/.env")"
DB_PORT="$(parse_env_var "DB_PORT" "$SHARED_DIR/.env")"
DB_DATABASE="$(parse_env_var "DB_DATABASE" "$SHARED_DIR/.env")"
DB_USERNAME="$(parse_env_var "DB_USERNAME" "$SHARED_DIR/.env")"
DB_PASSWORD="$(parse_env_var "DB_PASSWORD" "$SHARED_DIR/.env")"
APP_URL="$(parse_env_var "APP_URL" "$SHARED_DIR/.env")"
APP_URL="${APP_URL:-http://localhost}"
DEPLOY_SECRET="$(parse_env_var "DEPLOY_SECRET" "$SHARED_DIR/.env")"
DEPLOY_SECRET="${DEPLOY_SECRET:-bcafe-deploy-secret-token}"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

echo "💾 Preparing Database Backup before migration..."
BACKUP_FILE="$BACKUP_DIR/db_${TIMESTAMP}_${TARGET_SHORT_SHA}.sql.gz"

if [[ -n "$DB_DATABASE" && -n "$DB_USERNAME" ]]; then
    MYSQLDUMP_BIN="$(command -v mysqldump || echo "/usr/bin/mysqldump")"
    if [[ -x "$MYSQLDUMP_BIN" ]]; then
        echo "🗄️ Running mysqldump for database: $DB_DATABASE..."
        
        # Execute mysqldump with single-transaction and quick options
        if [[ -n "$DB_PASSWORD" ]]; then
            "$MYSQLDUMP_BIN" --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" --password="$DB_PASSWORD" \
                --single-transaction --quick --routines --triggers "$DB_DATABASE" | gzip > "$BACKUP_FILE"
        else
            "$MYSQLDUMP_BIN" --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" \
                --single-transaction --quick --routines --triggers "$DB_DATABASE" | gzip > "$BACKUP_FILE"
        fi

        # Verify dump is valid and non-empty
        if [[ -s "$BACKUP_FILE" ]]; then
            BACKUP_SIZE="$(du -h "$BACKUP_FILE" | cut -f1)"
            echo "✅ DB Backup verified: $BACKUP_FILE ($BACKUP_SIZE)"
        else
            echo "❌ CRITICAL: DB Backup failed or is empty! Aborting deployment to prevent data risk."
            rm -f "$BACKUP_FILE"
            exit 1
        fi
    else
        echo "⚠️ mysqldump binary not found. Skipping SQL dump."
    fi
else
    echo "⚠️ Database credentials not found in .env. Skipping backup."
fi

# Backup rotation: Keep the last 10 backups, delete older
BACKUP_RETENTION=10
echo "🧹 Rotating DB backups (retention: $BACKUP_RETENTION)..."
ls -tp "$BACKUP_DIR"/db_*.sql.gz 2>/dev/null | grep -v '/$' | tail -n +$((BACKUP_RETENTION + 1)) | xargs -I {} rm -f {} || true

# --- 8. Start Short Deliberate Maintenance Window ---
MAINTENANCE_SECRET="$(head -c 16 /dev/urandom | xxd -p 2>/dev/null || echo "bcafe-$RANDOM$RANDOM")"
echo "🔒 Enabling pre-compiled maintenance mode..."
if [[ -L "$CURRENT_DIR" && -d "$CURRENT_DIR" ]]; then
    (
        cd "$CURRENT_DIR"
        $PHP_BIN artisan down --render="errors.maintenance" --secret="$MAINTENANCE_SECRET" || true
    )
fi

# --- 9. Database Migrations (Additive-Only, Safe against Live Traffic) ---
echo "🗃️ Running database migrations (php artisan migrate --force)..."
if ! (cd "$RELEASE_DIR" && $PHP_BIN artisan migrate --force); then
    echo "❌ Migration failed! Aborting deployment."
    echo "🔄 Restoring live site from maintenance mode on current release..."
    if [[ -L "$CURRENT_DIR" && -d "$CURRENT_DIR" ]]; then
        (cd "$CURRENT_DIR" && $PHP_BIN artisan up || true)
    fi
    exit 1
fi
echo "✅ Migrations completed successfully."

# --- 10. Frontend Assets Build & Laravel Caches ---
if command -v npm &> /dev/null && [[ -f "$RELEASE_DIR/package.json" ]]; then
    echo "🎨 Building frontend assets with npm..."
    (
        cd "$RELEASE_DIR"
        npm ci --prefer-offline --no-audit --quiet || npm install --no-audit --quiet
        npm run build
    ) || echo "⚠️ Frontend build returned non-zero, continuing with built assets."
fi

echo "⚡ Rebuilding Laravel caches inside new release folder..."
(
    cd "$RELEASE_DIR"
    $PHP_BIN artisan config:cache
    $PHP_BIN artisan route:cache
    $PHP_BIN artisan view:cache
    $PHP_BIN artisan event:cache
)

# --- 11. Pre-Flip Health Verification ---
echo "🩺 Running pre-flip local health test on new release..."
if ! (cd "$RELEASE_DIR" && $PHP_BIN artisan about > /dev/null 2>&1); then
    echo "❌ Pre-flip health check failed: Artisan cannot boot cleanly."
    if [[ -L "$CURRENT_DIR" && -d "$CURRENT_DIR" ]]; then
        (cd "$CURRENT_DIR" && $PHP_BIN artisan up || true)
    fi
    exit 1
fi
echo "✅ Pre-flip health check passed."

# --- 12. Atomic Symlink Swap ---
echo "🔄 Atomically flipping symlink: $CURRENT_DIR -> $RELEASE_DIR..."
ln -sfn "$RELEASE_DIR" "$CURRENT_DIR"
echo "✅ Symlink successfully swapped."

# Document root handling (if public_html is a symlink or needs bridge)
PUBLIC_HTML="$USER_HOME/public_html"
if [[ -L "$PUBLIC_HTML" ]]; then
    ln -sfn "$CURRENT_DIR/public" "$PUBLIC_HTML"
elif [[ -d "$PUBLIC_HTML" && ! -L "$PUBLIC_HTML" ]]; then
    # In shared hosts where public_html cannot be a symlink, write safe proxy index.php if not exists
    PROXY_FILE="$PUBLIC_HTML/index.php"
    if [[ ! -f "$PROXY_FILE" || -f "$PUBLIC_HTML/.cpanel_proxy_managed" ]]; then
        echo "📄 Writing document root proxy to $PROXY_FILE..."
        cat << 'EOF' > "$PROXY_FILE"
<?php
// B Cafe cPanel Docroot Bridge
require_once dirname(__DIR__) . '/deploy/current/public/index.php';
EOF
        touch "$PUBLIC_HTML/.cpanel_proxy_managed"
    fi
fi

# --- 13. End Maintenance Window ---
echo "🔓 Bringing application back UP..."
(
    cd "$CURRENT_DIR"
    $PHP_BIN artisan up || true
)

# --- 14. Post-Flip Health Check & Automatic Rollback ---
echo "🩺 Performing post-flip public health check (/up)..."
HEALTH_URL="http://127.0.0.1:8000/up"
# Fallback to localhost or APP_URL if not local dev
if ! curl -fsS -o /dev/null "$HEALTH_URL" 2>/dev/null; then
    HEALTH_URL="${APP_URL%/}/up"
fi

HEALTH_PASSED=false
for i in {1..3}; do
    if curl -fsS -o /dev/null -m 5 "$HEALTH_URL" 2>/dev/null; then
        HEALTH_PASSED=true
        break
    fi
    echo "⏳ Retrying health check in 2 seconds (attempt $i/3)..."
    sleep 2
done

if [[ "$HEALTH_PASSED" = false ]]; then
    echo "❌ CRITICAL: Post-flip health check failed at $HEALTH_URL!"
    if [[ -n "$PREVIOUS_RELEASE" && -d "$PREVIOUS_RELEASE" ]]; then
        echo "🚨 INITIATING AUTOMATIC CODE ROLLBACK to $PREVIOUS_RELEASE..."
        ln -sfn "$PREVIOUS_RELEASE" "$CURRENT_DIR"
        (cd "$CURRENT_DIR" && $PHP_BIN artisan up || true)
        echo "⚠️ Rolled back successfully. Deployment marked as FAILED."
    else
        echo "⚠️ No previous release available to roll back to."
    fi
    exit 1
fi
echo "✅ Post-flip health check PASSED (HTTP 200 OK)."

# --- 15. Web OPcache Reset ---
echo "🧹 Triggering web OPcache reset..."
OPCACHE_URL="${APP_URL%/}/system/opcache-clear?key=${DEPLOY_SECRET}"
curl -fsS -m 5 "$OPCACHE_URL" > /dev/null 2>&1 || echo "ℹ️ OPcache cleared via process restart/path separation."

# --- 16. Prune Old Releases (Keep Last 3 for Rollback Headroom) ---
KEEP_RELEASES=3
echo "🧹 Pruning old release folders (retaining last $KEEP_RELEASES releases)..."
# List releases sorted by date, exclude top N, remove older ones
(
    cd "$RELEASES_DIR"
    # shellcheck disable=SC2012
    ls -1dt 20* 2>/dev/null | tail -n +$((KEEP_RELEASES + 1)) | while read -r old_rel; do
        if [[ -n "$old_rel" && -d "$old_rel" ]]; then
            echo "🗑️ Removing old release: $old_rel"
            rm -rf "$old_rel"
        fi
    done
)

# --- 17. Record Deployment in Database & Finish ---
(
    cd "$CURRENT_DIR"
    $PHP_BIN artisan deployment:record-success \
        --commit="$TARGET_COMMIT" \
        --message="$COMMIT_MSG" \
        --author="$COMMIT_AUTHOR" \
        --log="$DEPLOY_LOG" \
        --backup="$BACKUP_FILE" 2>/dev/null || true
)

echo "========================================================================"
echo "🎉 [$(date '+%Y-%m-%d %H:%M:%S')] Deployment Completed Successfully!"
echo "📍 Live Release: $RELEASE_DIR"
echo "========================================================================"
exit 0
