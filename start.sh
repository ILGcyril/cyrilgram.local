#!/bin/bash

echo "🔄 Waiting for database to be ready..."
sleep 15

echo "📦 Running migrations..."
php artisan migrate --force

# Очищаем кэш конфигурации
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "🚀 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}