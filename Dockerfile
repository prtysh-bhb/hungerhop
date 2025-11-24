# Use FrankenPHP base image (PHP + Caddy)
FROM dunglas/frankenphp:latest-php8.2

# Install PHP extensions
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

# Create necessary storage/cache folders
RUN mkdir -p /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /app/bootstrap/cache

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy full application source
COPY . .

# Permissions
RUN chown -R www-data:www-data /app \
 && chmod -R 775 /app/storage /app/bootstrap/cache

# Laravel post-install steps (THIS FIXES THE LOGIN 500)
RUN composer dump-autoload --optimize --classmap-authoritative && \
    php artisan key:generate --force && \
    php artisan storage:link && \
    php artisan optimize

# Copy Caddy configuration for FrankenPHP
COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
