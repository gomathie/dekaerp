#!/bin/bash
set -e

APP_DIR="/var/www/dekaerp"
cd "$APP_DIR"

log() { echo "[aureus-entrypoint] $(date '+%Y-%m-%d %H:%M:%S') $*"; }

# Driver selection. Defaults to mysql so existing deployments keep the
# self-contained internal-MySQL behaviour they had before pgsql was an option.
DB_CONNECTION="${DB_CONNECTION:-mysql}"

case "$DB_CONNECTION" in
    mysql|mariadb) DEFAULT_DB_PORT=3306 ;;
    pgsql)         DEFAULT_DB_PORT=5432 ;;
    *)
        log "ERROR: unsupported DB_CONNECTION '${DB_CONNECTION}'. Supported: mysql, mariadb, pgsql."
        exit 1
        ;;
esac

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-$DEFAULT_DB_PORT}"
DB_DATABASE="${DB_DATABASE:-aureus}"
DB_USERNAME="${DB_USERNAME:-aureus}"
DB_PASSWORD="${DB_PASSWORD:-aureus}"

# A managed database (Neon, RDS, Cloud SQL) is addressed either by DB_URL or by
# a non-local DB_HOST. Either way the bundled MySQL server must stay down.
is_managed_database() {
    [[ -n "$DB_URL" ]] && return 0
    [[ "$DB_CONNECTION" == "pgsql" ]] && return 0
    [[ "$DB_HOST" != "127.0.0.1" && "$DB_HOST" != "localhost" ]] && return 0
    return 1
}

if is_managed_database; then
    if [[ -n "$DB_URL" ]]; then
        log "Mode: EXTERNAL ${DB_CONNECTION} (via DB_URL)"
    else
        log "Mode: EXTERNAL ${DB_CONNECTION} (${DB_HOST}:${DB_PORT})"
    fi
else
    log "Mode: LOCAL ${DB_CONNECTION} (${DB_HOST}:${DB_PORT})"
    log "WARNING: no managed database detected. This image ships no database"
    log "         server, so DB_HOST must point at a reachable host."
fi

sed_escape() { printf '%s' "$1" | sed -e 's/[\\&|]/\\&/g'; }

# Rewrites KEY=... in .env, appending the line when the key is absent so that
# newer keys (DB_URL, DB_SSLMODE) work against an older .env.example.
set_env() {
    local key="$1" val
    val=$(sed_escape "$2")

    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${val}|" .env
    else
        printf '%s=%s\n' "$key" "$2" >> .env
    fi
}

log "Applying runtime environment overrides..."
set_env DB_CONNECTION "$DB_CONNECTION"

if [ -n "$DB_URL" ]; then
    # DB_URL overrides host, port, database and credentials wholesale, so the
    # discrete keys are blanked rather than left holding stale values.
    set_env DB_URL      "$DB_URL"
    set_env DB_HOST     ""
    set_env DB_PORT     ""
    set_env DB_DATABASE ""
    set_env DB_USERNAME ""
    set_env DB_PASSWORD ""
else
    set_env DB_URL      ""
    set_env DB_HOST     "$DB_HOST"
    set_env DB_PORT     "$DB_PORT"
    set_env DB_DATABASE "$DB_DATABASE"
    set_env DB_USERNAME "$DB_USERNAME"
    set_env DB_PASSWORD "$DB_PASSWORD"
fi

# SSL is never inferred. Requiring it because the driver happens to be pgsql
# breaks every Postgres that does not offer TLS - a container on the same
# network, or a server reached over a private link - with:
#
#   server does not support SSL, but SSL was required
#
# Managed providers that need it say so in their own connection string
# (Neon appends ?sslmode=require, which Laravel's URL parser honours), and
# DB_SSLMODE is there to set it explicitly for anything that does not.
if [ -n "$DB_SSLMODE" ]; then
    set_env DB_SSLMODE "$DB_SSLMODE"
fi

set_env APP_ENV "${APP_ENV:-production}"
set_env APP_DEBUG "${APP_DEBUG:-false}"

# Log to the container's stdout so the orchestrator collects it. The default
# stack writes to storage/logs/laravel.log, which nothing reads and which is
# discarded on every redeploy.
set_env LOG_CHANNEL "${LOG_CHANNEL:-stderr}"
set_env LOG_LEVEL "${LOG_LEVEL:-info}"

[ -n "$APP_URL" ]      && set_env APP_URL      "$APP_URL"
[ -n "$APP_KEY" ]      && set_env APP_KEY      "$APP_KEY"
[ -n "$APP_NAME" ]     && set_env APP_NAME     "\"${APP_NAME}\""
[ -n "$APP_LOCALE" ]   && set_env APP_LOCALE   "$APP_LOCALE"
[ -n "$APP_CURRENCY" ] && set_env APP_CURRENCY "$APP_CURRENCY"
[ -n "$APP_TIMEZONE" ] && set_env APP_TIMEZONE "$APP_TIMEZONE"

if is_managed_database; then
    log "Waiting for ${DB_CONNECTION} to become reachable..."

    # PHP does the probing so DB_URL is parsed by the same rules Laravel uses,
    # and so a serverless database that is scaling up from zero simply retries.
    probe='
        $url = getenv("DB_URL") ?: null;
        $driver = getenv("DB_CONNECTION") ?: "mysql";
        $sslmode = getenv("DB_SSLMODE") ?: null;

        if ($url) {
            $p = parse_url($url);
            parse_str($p["query"] ?? "", $q);
            $driver = ($p["scheme"] ?? "") === "mysql" ? "mysql" : "pgsql";
            $host = $p["host"] ?? "";
            $port = $p["port"] ?? ($driver === "mysql" ? 3306 : 5432);
            $db = ltrim($p["path"] ?? "", "/");
            // rawurldecode, not urldecode, to match Laravel'"'"'s
            // ConfigurationUrlParser - they differ on "+", and a generated
            // password containing one would otherwise fail here but work
            // in the application.
            $user = rawurldecode($p["user"] ?? "");
            $pass = rawurldecode($p["pass"] ?? "");
            $sslmode = $q["sslmode"] ?? $sslmode;
        } else {
            $host = getenv("DB_HOST");
            $port = getenv("DB_PORT");
            $db = getenv("DB_DATABASE");
            $user = getenv("DB_USERNAME");
            $pass = getenv("DB_PASSWORD");
        }

        $dsn = $driver === "mysql"
            ? sprintf("mysql:host=%s;port=%s", $host, $port)
            : sprintf("pgsql:host=%s;port=%s;dbname=%s%s", $host, $port, $db,
                $sslmode ? ";sslmode=".$sslmode : "");

        try {
            new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage().PHP_EOL);
            exit(1);
        }
    '

    for i in $(seq 1 60); do
        if DB_URL="$DB_URL" DB_CONNECTION="$DB_CONNECTION" DB_SSLMODE="$DB_SSLMODE" \
           DB_HOST="$DB_HOST" DB_PORT="$DB_PORT" DB_DATABASE="$DB_DATABASE" \
           DB_USERNAME="$DB_USERNAME" DB_PASSWORD="$DB_PASSWORD" \
           php -r "$probe" 2>/dev/null; then
            log "Database is reachable."
            break
        fi
        if [ "$i" -eq 60 ]; then
            log "ERROR: cannot reach the ${DB_CONNECTION} database after 60s."
            DB_URL="$DB_URL" DB_CONNECTION="$DB_CONNECTION" DB_SSLMODE="$DB_SSLMODE" \
            DB_HOST="$DB_HOST" DB_PORT="$DB_PORT" DB_DATABASE="$DB_DATABASE" \
            DB_USERNAME="$DB_USERNAME" DB_PASSWORD="$DB_PASSWORD" \
            php -r "$probe" || true
            exit 1
        fi
        sleep 1
    done
fi

# A persistent volume mounted at storage/app starts empty and owned by root,
# hiding whatever the image created there. Recreate the directories the
# application writes to, and hand them to the web user, before anything runs.
log "Ensuring storage directories exist..."

mkdir -p \
    storage/app/public \
    storage/app/private \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# public/storage is a symlink into storage/app/public. It is created at build
# time, but a volume mount can leave it dangling, and storage:link is a no-op
# when it is already correct.
php artisan storage:link --no-interaction 2>/dev/null || true

log "Refreshing cached configuration..."

php artisan optimize:clear --no-interaction 2>/dev/null || true

log "Starting services via Supervisor..."

exec "$@"
