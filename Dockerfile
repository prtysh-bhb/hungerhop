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

# Create necessary cache directories before autoloader
RUN mkdir -p /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /app/bootstrap/cache

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies without scripts first
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application source
COPY . .

# Permissions before autoload
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Generate autoload
RUN composer dump-autoload --optimize --classmap-authoritative

# Generate dynamic Caddyfile
RUN echo $'{\n\
    frankenphp\n\
    order php_server before file_server\n\
}\n\
\n\
:{$PORT} {\n\
    root * /app/public\n\
    php_server\n\
    encode gzip\n\
    file_server\n\
    log {\n\
        output stdout\n\
        format console\n\
        level INFO\n\
    }\n\
}' > /etc/caddy/Caddyfile

EXPOSE 8080

# Start FrankenPHP server
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
