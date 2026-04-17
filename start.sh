#!/bin/bash

echo "🔄 Waiting for database to be ready..."
sleep 15

echo "📦 Running migrations..."
php artisan migrate --force

echo "📡 Starting Reverb server..."
php artisan reverb:start &

echo "🚀 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}