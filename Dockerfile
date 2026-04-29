# =========================
# Stage 1: Composer Vendor
# =========================
FROM composer:2 AS vendor

WORKDIR /app

# GAK USAH install apa-apa di sini (biar ga ribet Alpine vs Debian)
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

# Install extension WAJIB di sini (Debian based → apt-get aman)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy vendor
COPY --from=vendor /app/vendor /var/www/vendor

# Copy project
COPY . .

# Permission
RUN chmod -R 775 storage bootstrap/cache

# Run Laravel (Railway)
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php -S 0.0.0.0:${PORT} -t public