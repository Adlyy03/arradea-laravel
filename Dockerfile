# =========================
# Stage 1: Composer Vendor (DEBIAN, bukan Alpine!)
# =========================
FROM composer:2-bookworm AS vendor

WORKDIR /app

# Install zip tools (cukup ini, JANGAN install extension PHP di sini)
RUN apt-get update && apt-get install -y \
    zip unzip git


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

# Copy composer
COPY --from=composer:2-bookworm /usr/bin/composer /usr/bin/composer

# Copy vendor dari stage sebelumnya
COPY --from=vendor /app/vendor /var/www/vendor

# Copy semua project
COPY . .

# Permission (jangan 777 brutal, tapi ya udah amanin dulu)
RUN chmod -R 775 storage bootstrap/cache

# =========================
# Run Laravel (Railway ready)
# =========================
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php -S 0.0.0.0:${PORT} -t public