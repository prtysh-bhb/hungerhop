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

# Create necessary Laravel folders
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Copy composer files first (better caching)
COPY composer.json composer.lock ./

# Install PHP dependencies, BUT DON'T RUN SCRIPTS (so no artisan at build time)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy full application source
COPY . .

# Fix permissions for storage and cache
RUN chown -R www-data:www-data /app \
 && chmod -R 775 storage bootstrap/cache

# Generate optimized autoloader ONLY (still no scripts here)
RUN composer dump-autoload --optimize --classmap-authoritative --no-scripts

# Copy Caddy configuration for FrankenPHP
COPY Caddyfile /etc/caddy/Caddyfile

# Create entrypoint script that will run AFTER env + DB are available
RUN echo '#!/bin/sh'                                      >  /entrypoint.sh \
 && echo 'php artisan config:clear'                      >> /entrypoint.sh \
 && echo 'php artisan cache:clear'                       >> /entrypoint.sh \
 && echo 'php artisan route:clear'                       >> /entrypoint.sh \
 && echo 'php artisan view:clear'                        >> /entrypoint.sh \
 && echo 'php artisan key:generate --force'              >> /entrypoint.sh \
 && echo 'php artisan package:discover --ansi || true'   >> /entrypoint.sh \
 && echo 'php artisan migrate --force || true'           >> /entrypoint.sh \
 && echo 'php artisan db:seed --force || true'           >> /entrypoint.sh \
 && echo 'php artisan storage:link || true'              >> /entrypoint.sh \
 && echo 'php artisan optimize'                          >> /entrypoint.sh \
 && echo 'exec frankenphp run --config /etc/caddy/Caddyfile' >> /entrypoint.sh \
 && chmod +x /entrypoint.sh

EXPOSE 8080

# Start Laravel + FrankenPHP via entrypoint
CMD ["/entrypoint.sh"]
