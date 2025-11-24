#!/bin/sh
set -e

echo "🚀 Starting Laravel application..."

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ensure APP_KEY is set
echo "🔑 Checking APP_KEY..."
php artisan key:generate --force

# Discover packages
echo "📦 Discovering packages..."
php artisan package:discover --ansi || true

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force || true

# Seed database (optional, remove if not needed)
echo "🌱 Seeding database..."
php artisan db:seed --force || true

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link || true

# Optimize application
echo "⚡ Optimizing application..."
php artisan optimize

echo "✅ Laravel initialization complete!"
echo "🚀 Starting FrankenPHP server..."

# Start FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
