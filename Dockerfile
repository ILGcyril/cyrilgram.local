FROM dunglas/frankenphp:php8.4-bookworm

# Устанавливаем Node.js 20+ через NodeSource
RUN apt-get update && apt-get install -y git unzip zip ca-certificates curl && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

RUN echo "legacy-peer-deps=true" > .npmrc
RUN npm install --legacy-peer-deps && npm run build

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
