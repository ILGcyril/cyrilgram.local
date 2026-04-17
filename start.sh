#!/bin/bash
sleep 15
php artisan migrate --force

# Очищаем кэш чтобы env() и config() работали
php artisan config:clear
php artisan cache:clear

php artisan serve --host=0.0.0.0 --port=${PORT:-8000}