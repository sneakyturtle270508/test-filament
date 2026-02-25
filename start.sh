#!/bin/sh
set -e

# Sett rettigheter
chown -R www-data:www-data /var/www
chmod -R 755 /var/www

# Kjør migrasjoner automatisk
php artisan migrate --force

# Start Laravel server på Render-port
php artisan serve --host=0.0.0.0 --port=10000