# Use FrankenPHP base image (includes PHP + Caddy)
FROM dunglas/frankenphp:latest-php8.2

# Install system dependencies and PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    gd \
    zip \
    opcache \
    intl \
    bcmath \
    exif

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Create necessary directories with proper permissions
RUN mkdir -p /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /app/bootstrap/cache

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (production only, skip scripts to avoid cache errors)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Set permissions BEFORE autoload to ensure cache directories are writable
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Generate optimized autoloader (skip all scripts to avoid cache path errors)
RUN composer dump-autoload --optimize --no-scripts --classmap-authoritative

# Create a Caddyfile for FrankenPHP
RUN echo $'{\n\
    frankenphp\n\
    order php_server before file_server\n\
}\n\
\n\
:{$PORT:8000} {\n\
    root * /app/public\n\
    php_server\n\
    encode gzip\n\
    file_server\n\
    log {\n\
        output stdout\n\
        format console\n\
    }\n\
}' > /etc/caddy/Caddyfile

EXPOSE 8000

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
