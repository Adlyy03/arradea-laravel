### Production multi-stage Dockerfile for Railway

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

FROM php:8.2-fpm
ARG USER=www-data
ENV PORT=8080

# System deps
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    default-mysql-client \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip bcmath mbstring pcntl intl xml opcache \
  && rm -rf /var/lib/apt/lists/*

# Copy composer vendor from builder
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor

# Copy app
COPY . /var/www/html

# Copy composer binary from composer image (for runtime fallback)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Ensure permissions
RUN chown -R ${USER}:${USER} /var/www/html/storage /var/www/html/bootstrap/cache \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx template and entrypoint
COPY docker/nginx/production.conf.template /etc/nginx/conf.d/default.conf.template
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

STOPSIGNAL SIGTERM

CMD ["/usr/local/bin/entrypoint.sh"]
