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
    bcmath \
    gd \
    zip \
    intl \
    exif \
    pcntl \
    redis \
    sockets \
    mbstring \
    curl \
    xml \
    xmlwriter \
    simplexml \
    fileinfo

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction


# ==========================================================================
# Stage 3: Production Image
# ==========================================================================
FROM dunglas/frankenphp:latest-php8.2-alpine

WORKDIR /app

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

COPY --chown=www-data:www-data Caddyfile /etc/caddy/Caddyfile

USER www-data

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --retries=5 \
  CMD wget -qO- http://localhost:8080/ || exit 1

# 🚨 THIS LINE IS REQUIRED (otherwise app will never start)
ENTRYPOINT ["frankenphp", "run", "--config=/etc/caddy/Caddyfile"]
