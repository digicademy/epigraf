#!/bin/bash
set -euo pipefail

log() { printf '[entrypoint] %s\n' "$*"; }
die() { printf '[entrypoint] ERROR: %s\n' "$*" >&2; exit 1; }

MYSQL_HOST="${MYSQL_HOST:-sql}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="${MYSQL_USER:-epigraf}"
MYSQL_PASS="${MYSQL_PASS:-epigraf}"
MYSQL_DATABASE="${MYSQL_DATABASE:-epigraf}"

ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"

DEMO_DATABASE="${DEMO_DATABASE:-epi_movies}"
DEMO_PRESET="${DEMO_PRESET:-movies}"

# -----------------------------------------------------------------------------
# Prepare writable directories (named volumes are empty on first start)
# -----------------------------------------------------------------------------
mkdir -p /var/www/html/tmp/cache/{models,persistent,views,results,services,index} \
         /var/www/html/tmp/sessions \
         /var/www/html/logs \
         /var/www/data/{shared,databases,export}
chown -R www-data:www-data /var/www/html/tmp /var/www/html/logs /var/www/data

# -----------------------------------------------------------------------------
# Wait for the database server to accept connections
# -----------------------------------------------------------------------------
log "Waiting for ${MYSQL_HOST}:${MYSQL_PORT} ..."
for i in $(seq 1 90); do
    mariadb-admin ping -h"$MYSQL_HOST" -P"$MYSQL_PORT" \
        -u"$MYSQL_USER" -p"$MYSQL_PASS" --silent >/dev/null 2>&1 && break
    [ "$i" -eq 90 ] && die "Database not reachable after 180s."
    sleep 2
done
log "Database server is reachable."

sql_root() {
    mariadb -h"$MYSQL_HOST" -P"$MYSQL_PORT" -uroot -p"${MYSQL_ROOT_PASSWORD:-root}" \
        -N -B -e "$1"
}

# -----------------------------------------------------------------------------
# Check whether the main schema exists. This has to happen BEFORE we create
# anything, otherwise the check would always be positive.
# On error we assume it exists, so we never re-initialise a live database.
# -----------------------------------------------------------------------------
DB_EXISTS="$(sql_root "
    SELECT COUNT(*) FROM information_schema.schemata
    WHERE schema_name = '${MYSQL_DATABASE}';
" 2>/dev/null || echo 1)"

# -----------------------------------------------------------------------------
# Privileges: Epigraf creates and drops project databases at runtime,
# so the application user needs privileges on *.* rather than a single schema.
# -----------------------------------------------------------------------------
sql_root "
    GRANT ALL PRIVILEGES ON *.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
" >/dev/null 2>&1 || log "WARNING: could not grant privileges."

as_www() { su -s /bin/bash www-data -c "cd /var/www/html && $*"; }

# -----------------------------------------------------------------------------
# First-run initialisation
# -----------------------------------------------------------------------------
if [ "${INIT_DATABASE:-true}" = "true" ] && [ "${DB_EXISTS:-1}" -eq 0 ]; then
    log "Database '${MYSQL_DATABASE}' does not exist - running initialisation ..."

    sql_root "CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}\`
              CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >/dev/null

    as_www "bin/cake database init" \
        || die "'bin/cake database init' failed."

    log "Creating user '${ADMIN_USER}' ..."
    as_www "bin/cake user add '${ADMIN_USER}' admin '${ADMIN_PASS}' '${ADMIN_USER}'" \
        || log "WARNING: could not create user (it may already exist)."

    if [ "${INIT_DEMO:-true}" = "true" ]; then
        log "Creating demo database '${DEMO_DATABASE}' (preset: ${DEMO_PRESET}) ..."
        as_www "bin/cake database init --database '${DEMO_DATABASE}' --preset '${DEMO_PRESET}'" \
            || log "WARNING: could not create demo database."
    fi

    log "Initialisation done. Login: ${ADMIN_USER} / ${ADMIN_PASS}"
else
    log "Database '${MYSQL_DATABASE}' already exists - skipping initialisation."
fi

# -----------------------------------------------------------------------------
# Clear caches and hand over to Apache
# -----------------------------------------------------------------------------
as_www "bin/cake cache clear_all" >/dev/null 2>&1 || true

# Remove a stale PID file left behind by a hard kill
rm -f /var/run/apache2/apache2.pid

log "Starting Apache."
exec "$@"