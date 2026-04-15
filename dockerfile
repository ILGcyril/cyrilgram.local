# Use PHP with FrankenPHP
FROM dunglas/frankenphp:php8.3-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    nodejs \
    npm \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node dependencies and build
RUN npm install && npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 80

# Start command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]