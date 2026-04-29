# =========================
# Stage 1: Vendor (PHP + Composer)
# =========================
FROM php:8.2-cli AS vendor

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install zip mbstring gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

# Tambahin memory biar ga mati mendadak
ENV COMPOSER_MEMORY_LIMIT=-1

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist

# =========================
# Stage 2: App (PHP-FPM)
# =========================
FROM php:8.2-fpm

WORKDIR /var/www

# Install extension lagi untuk runtime
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy vendor dari stage vendor
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