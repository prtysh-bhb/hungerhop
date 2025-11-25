#!/bin/sh
set -e

echo "🚀 Starting Laravel application..."

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY is not set!"
    echo "Please add APP_KEY in Railway variables"
fi

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear || echo "⚠️  Config clear failed"
php artisan cache:clear || echo "⚠️  Cache clear failed"
php artisan route:clear || echo "⚠️  Route clear failed"
php artisan view:clear || echo "⚠️  View clear failed"

# Discover packages
echo "📦 Discovering packages..."
php artisan package:discover --ansi || echo "⚠️  Package discovery failed"

# Check database connection before running migrations
echo "🔍 Checking database connection..."
if php artisan db:show 2>/dev/null; then
    echo "✅ Database connected!"
    
    # Run migrations
    echo "🗄️  Running database migrations..."
    php artisan migrate --force || echo "⚠️  Migration failed"
    
    # Seed database (optional, remove if not needed)
    echo "🌱 Seeding database..."
    php artisan db:seed --force || echo "⚠️  Seeding failed"
else
    echo "⚠️  Database not available, skipping migrations"
    echo "Please check DB_HOST, DB_USERNAME, DB_PASSWORD variables in Railway"
fi

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link || echo "⚠️  Storage link failed"

# Optimize application
echo "⚡ Optimizing application..."
php artisan optimize || echo "⚠️  Optimization failed"

echo "✅ Laravel initialization complete!"
echo "🚀 Starting FrankenPHP server..."

# Start FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
