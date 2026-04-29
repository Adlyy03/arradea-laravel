# =========================
# Stage 1: Vendor
# =========================
FROM php:8.3-cli AS vendor

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install zip mbstring gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

ENV COMPOSER_MEMORY_LIMIT=-1

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist

# =========================
# Stage 2: App
# =========================
FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor /var/www/vendor

COPY . .

RUN chmod -R 775 storage bootstrap/cache

CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php -S 0.0.0.0:${PORT} -t public