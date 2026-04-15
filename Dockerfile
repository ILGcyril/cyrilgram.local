FROM dunglas/frankenphp:php8.4-bookworm

RUN apt-get update && apt-get install -y git unzip zip nodejs npm ca-certificates && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Добавь .npmrc для игнорирования peer deps
RUN echo "legacy-peer-deps=true" > .npmrc
RUN npm install --legacy-peer-deps && npm run build

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
