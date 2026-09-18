# =========================================================
# STAGE 1: Compilación de Frontend con Vite
# =========================================================
FROM node:22-alpine AS frontend-builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --prefer-offline --no-audit

COPY resources/ ./resources/
COPY public/ ./public/
COPY vite.config.js ./
RUN npm run build

# =========================================================
# STAGE 2: Dependencias PHP con Composer
# =========================================================
FROM php:8.2-cli-alpine AS composer-builder
WORKDIR /app

RUN apk add --no-cache git curl unzip libzip-dev \
    && docker-php-ext-install zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev

# =========================================================
# STAGE 3: Imagen Final de Producción (Nginx + PHP-FPM)
# =========================================================
FROM php:8.2-fpm-alpine

# Instalar Nginx, Supervisor, librerías de sistema y extensiones PHP requeridas
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    netcat-openbsd \
    python3 \
    py3-pillow \
    libpng \
    libjpeg-turbo \
    libwebp \
    freetype \
    libzip \
    icu-libs \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        opcache \
        gd \
        zip \
        intl \
        bcmath \
        pcntl \
        exif \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

WORKDIR /var/www/html

# Copiar configuraciones
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copiar código fuente
COPY . .

# Copiar vendor optimizado del stage de composer
COPY --from=composer-builder /app/vendor ./vendor

# Copiar assets estáticos compilados por Vite
COPY --from=frontend-builder /app/public/build ./public/build

# Permisos seguros para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
