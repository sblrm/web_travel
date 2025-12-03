#!/bin/sh
set -e

echo "🚀 Starting CulturalTrip Application..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
until php artisan db:show > /dev/null 2>&1; do
    echo "⏳ Database is unavailable - sleeping"
    sleep 2
done
echo "✅ Database is ready!"

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --no-interaction
fi

# Link storage if not already linked
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Linking storage..."
    php artisan storage:link --no-interaction
fi

# Run database migrations
if [ "$APP_ENV" = "production" ]; then
    echo "🗄️  Running production migrations..."
    php artisan migrate --force --no-interaction
else
    echo "🗄️  Running development migrations..."
    php artisan migrate --no-interaction
    
    # Seed database in development
    echo "🌱 Seeding database..."
    php artisan db:seed --no-interaction || echo "⚠️  Seeding skipped or failed"
fi

# Clear and cache configuration (production only)
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    echo "🧹 Clearing cache for development..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

# Fix permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Application is ready!"
echo "🌐 Visit: http://localhost:8000"
echo "🛡️  Admin: http://localhost:8000/admin"

# Execute the main command
exec "$@"
