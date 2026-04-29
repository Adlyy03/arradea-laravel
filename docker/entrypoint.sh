#!/usr/bin/env bash
set -e

# Ensure PORT is set (Railway provides it)
: ${PORT:=8080}

# Render nginx config
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# Ensure storage & cache writable
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Install composer dependencies (if vendor not present)
if [ ! -d "/var/www/html/vendor" ] || [ -z "$(ls -A /var/www/html/vendor)" ]; then
  composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
fi

# Run Laravel commands
php artisan config:cache || true
php artisan route:cache || true

# Run migrations (may fail if DB not ready; retry loop)
MAX_RETRIES=10
count=0
until php artisan migrate --force; do
  count=$((count+1))
  if [ $count -ge $MAX_RETRIES ]; then
    echo "Migrations failed after $MAX_RETRIES attempts"
    break
  fi
  echo "Waiting for DB... retrying ($count/$MAX_RETRIES)"
  sleep 5
done

# Start php-fpm and nginx in foreground
php-fpm -D
nginx -g 'daemon off;'
