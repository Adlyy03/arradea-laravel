FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpq-dev \
    libssl-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip bcmath mbstring pcntl sockets exif intl xml

RUN pecl channel-update pecl.php.net \
    && pecl install xdebug-3.2.2 || true \
    && docker-php-ext-enable xdebug || true

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel || true

EXPOSE 9000

CMD ["php-fpm"]
