#!/bin/sh

echo "============================================"
echo "Q-MATE Laravel Startup"
echo "============================================"

echo "Clearing Laravel caches..."
php artisan optimize:clear

echo "Running database migrations..."
php artisan migrate --force

echo "Caching Laravel configuration..."
php artisan config:cache

echo "Caching Laravel routes..."
php artisan route:cache

echo "Caching Laravel views..."
php artisan view:cache

echo "Starting PHP-FPM and Nginx..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf