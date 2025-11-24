# Use FrankenPHP base image (includes PHP + Caddy)
FROM dunglas/frankenphp:latest-php8.2

# Install system dependencies and PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    gd \
    zip \
    opcache \
    intl \
    bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Set permissions for Laravel
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage /app/bootstrap/cache

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
}' > /etc/caddy/Caddyfile

EXPOSE 8000

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
