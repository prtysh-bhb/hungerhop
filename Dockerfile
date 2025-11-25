# ==========================================================================
# Stage 1: Frontend Build (Node + Vite)
# ==========================================================================
FROM node:20-alpine AS frontend-builder

WORKDIR /build

COPY package.json package-lock.json* ./
RUN npm ci --prefer-offline --no-audit

COPY vite.config.js ./
COPY resources ./resources
RUN npm run build


# ==========================================================================
# Stage 2: Backend Dependencies (Composer)
# ==========================================================================
FROM dunglas/frankenphp:latest-php8.2-alpine AS backend-builder

WORKDIR /build

RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    bcmath \
    gd \
    zip \
    opcache \
    intl \
    exif \
    pcntl \
    redis \
    soap \
    sockets \
    mbstring \
    curl \
    xml \
    xmlwriter \
    simplexml \
    fileinfo \
    openssl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction


# ==========================================================================
# Stage 3: Production Image
# ==========================================================================
FROM dunglas/frankenphp:latest-php8.2-alpine

WORKDIR /app

# Install runtime PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    bcmath \
    gd \
    zip \
    opcache \
    intl \
    exif \
    pcntl \
    redis \
    soap \
    sockets \
    mbstring \
    curl \
    xml \
    xmlwriter \
    simplexml \
    fileinfo \
    openssl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=backend-builder --chown=www-data:www-data /build/vendor ./vendor
COPY --chown=www-data:www-data . .
COPY --from=frontend-builder --chown=www-data:www-data /build/public/dist ./public/dist

RUN mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/storage \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# PHP Configuration optimizations
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory.ini && \
    echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/upload.ini && \
    echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/upload.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/upload.ini && \
    echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

COPY --chown=www-data:www-data Caddyfile /etc/caddy/Caddyfile
COPY --chown=www-data:www-data entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --retries=5 \
  CMD wget -qO- http://localhost:8080/ || exit 1

# Run Laravel initialization then start FrankenPHP
ENTRYPOINT ["/entrypoint.sh"]
