FROM dunglas/frankenphp:php8.4-bookworm

RUN apt-get update && apt-get install -y git unzip zip ca-certificates curl && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    docker-php-ext-install pdo pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-scripts

RUN echo "legacy-peer-deps=true" > .npmrc
RUN npm install --legacy-peer-deps && npm run build

RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-www-data storage bootstrap/cache

# Запускаем миграции автоматически!
RUN php artisan migrate --force

EXPOSE 8000
ENV PORT=8000
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
