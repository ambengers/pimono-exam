#!/bin/bash
set -e

echo "Starting application initialization..."

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
until php -r "try { new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USERNAME:-pimono}', '${DB_PASSWORD:-password}'); exit(0); } catch (PDOException \$e) { exit(1); }" &> /dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is up - executing commands"

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache configuration
echo "Optimizing application..."
php artisan config:cache
# Only cache routes in production to allow route changes in development
if [ "$APP_ENV" = "production" ]; then
    php artisan route:cache
else
    php artisan route:clear
fi
php artisan view:cache

# Create necessary directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "Application initialization completed!"

# Execute the main command
exec "$@"

