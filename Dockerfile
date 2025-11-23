# ----- Build Stage -----
FROM node:18 AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ----- PHP Stage -----
FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Copy built assets
COPY --from=node-builder /app/public ./public

# Laravel optimizations
RUN composer install --optimize-autoloader --no-dev \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]
