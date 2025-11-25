# ==============================================================================
# Multi-Stage Build for Laravel 12 HungerHop Application with FrankenPHP
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Frontend Build (Node.js + Vite)
# ------------------------------------------------------------------------------
FROM node:20-alpine AS frontend-builder

WORKDIR /build

# Copy package files
COPY package.json package-lock.json* ./

# Install Node dependencies
RUN npm ci --prefer-offline --no-audit

# Copy Vite config and source files
COPY vite.config.js ./
COPY resources ./resources

# Build frontend assets
RUN npm run build

# ------------------------------------------------------------------------------
# Stage 2: PHP Dependencies
# ------------------------------------------------------------------------------
FROM dunglas/frankenphp:latest-php8.2-alpine AS php-builder

WORKDIR /build

# Install PHP extensions required by Laravel 12 and project dependencies
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    gd \
    zip \
    opcache \
    intl \
    bcmath \
    exif \
    pcntl \
    redis \
    soap \
    sockets \
    xml \
    xmlwriter \
    simplexml \
    fileinfo \
    mbstring \
    curl \
    openssl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (no scripts, no dev dependencies)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-progress \
    --no-interaction \
    --optimize-autoloader

# ------------------------------------------------------------------------------
# Stage 3: Production Image
# ------------------------------------------------------------------------------
FROM dunglas/frankenphp:latest-php8.2-alpine

LABEL maintainer="HungerHop Team"
LABEL description="Laravel 12 HungerHop Application with FrankenPHP"

# Install runtime PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    gd \
    zip \
    opcache \
    intl \
    bcmath \
    exif \
    pcntl \
    redis \
    soap \
    sockets \
    xml \
    xmlwriter \
    simplexml \
    fileinfo \
    mbstring \
    curl \
    openssl

# Install Composer for runtime (needed for package discovery)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Create Laravel directory structure with proper permissions
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

# Copy vendor from builder stage
COPY --from=php-builder --chown=www-data:www-data /build/vendor ./vendor

# Copy application code
COPY --chown=www-data:www-data . .

# Copy built frontend assets from frontend-builder
COPY --from=frontend-builder --chown=www-data:www-data /build/public/dist ./public/dist

# Generate optimized autoloader (without running scripts)
RUN composer dump-autoload --optimize --classmap-authoritative --no-scripts --no-dev

# Copy Caddy configuration for FrankenPHP
COPY --chown=www-data:www-data Caddyfile /etc/caddy/Caddyfile

# Copy and set executable permissions for entrypoint
COPY --chown=www-data:www-data entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

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

# Set proper ownership
RUN chown -R www-data:www-data /app

# Switch to www-data user for security
USER www-data

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Start application
ENTRYPOINT ["/entrypoint.sh"]