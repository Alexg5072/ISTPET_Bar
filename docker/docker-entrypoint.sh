#!/bin/sh
set -e

echo "==> Iniciando ISTPET Bar Container..."

# Asegurar directorios requeridos
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

# Crear symlink de storage si no existe
if [ ! -L /var/www/html/public/storage ]; then
    echo "==> Creando storage:link..."
    php artisan storage:link --force || true
fi

# Esperar a la base de datos si DB_HOST está definido y no es SQLite
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "==> Esperando conexión con la base de datos en $DB_HOST:${DB_PORT:-3306}..."
    until nc -z -v -w3 "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null; do
        echo "--> Esperando base de datos..."
        sleep 2
    done
    echo "==> Base de datos disponible."
fi

# Si se usa SQLite y el archivo no existe, crearlo
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    SQLITE_DB="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$SQLITE_DB" ]; then
        echo "==> Creando archivo SQLite: $SQLITE_DB"
        touch "$SQLITE_DB"
    fi
    chown www-data:www-data "$SQLITE_DB" || true
    chmod 664 "$SQLITE_DB" || true
fi

# Migraciones automáticas (activado por defecto si RUN_MIGRATIONS!=false)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Ejecutando migraciones de Laravel..."
    php artisan migrate --force || echo "Aviso: Error en migrate, continuando..."
fi

# Seeders automáticos (activado solo si RUN_SEEDERS=true)
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Ejecutando seeders de base de datos..."
    php artisan db:seed --force || echo "Aviso: Error en seeders, continuando..."
fi

# Optimización y caché de Laravel para producción
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "==> Optimizando Laravel para producción..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    echo "==> Limpiando caché en entorno de desarrollo..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

# Ajustar permisos finales para que PHP-FPM (usuario www-data) tenga control total
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Arrancando servicios con Supervisord..."
exec "$@"
