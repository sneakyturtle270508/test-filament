FROM php:8.3-fpm

WORKDIR /var/www

# Installer dependencies for Laravel + Filament
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libxml2-dev zlib1g-dev libzip-dev \
    libonig-dev pkg-config build-essential autoconf bison libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Safe git directory
RUN git config --global --add safe.directory /var/www

# Kopier prosjekt
COPY . .

# Rettigheter
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Composer install
RUN composer install --no-dev --optimize-autoloader

# Storage link
RUN php artisan storage:link

# Eksponer port for Render
EXPOSE 10000

CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=10000