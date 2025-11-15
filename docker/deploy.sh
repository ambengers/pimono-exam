#!/bin/bash

set -e

echo "🚀 Starting Pimono Application Deployment..."

# Check if .env file exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found. Creating from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ .env file created. Please update it with your configuration."
        echo "⚠️  Don't forget to set your database credentials and Pusher keys!"
        exit 1
    else
        echo "❌ .env.example not found. Please create a .env file manually."
        exit 1
    fi
fi

# Build and start containers
echo "📦 Building Docker images..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Clear and cache configuration
echo "⚡ Optimizing application..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

echo "✅ Deployment completed successfully!"
echo ""
echo "🌐 Application is running at: http://localhost:8000"
echo ""
echo "📋 Useful commands:"
echo "   - View logs: docker-compose logs -f app"
echo "   - Stop containers: docker-compose down"
echo "   - Restart containers: docker-compose restart"
echo "   - Check queue status: docker-compose exec app supervisorctl status"

