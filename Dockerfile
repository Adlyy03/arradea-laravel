# =========================
# Stage 1: Composer Vendor
# =========================
FROM composer:2 AS vendor

WORKDIR /app

# Install ext-zip BIAR composer ga error
RUN apk add --no-cache \
    libzip-dev zip unzip \
    && docker-php-ext-install zip

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist

# =========================
# Stage 2: App (PHP)
# =========================
FROM php:8.2-fpm

WORKDIR /var/www

# Install dependency + extension
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy vendor dari stage sebelumnya
COPY --from=vendor /app/vendor /var/www/vendor

# Copy semua project
COPY . .

# Permission (biar ga error storage)
RUN chmod -R 777 storage bootstrap/cache

# =========================
# Run Laravel
# =========================
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT}