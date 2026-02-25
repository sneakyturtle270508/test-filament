FROM php:8.2-cli

# Installer system dependencies inkludert oniguruma for mbstring
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    libxml2-dev \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    pkg-config \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip tokenizer

# Installer Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Kopier prosjektfiler
COPY . .

# Rettigheter
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Lag storage link
RUN php artisan storage:link

# Eksponer port
EXPOSE 10000

# Start server og migrering
CMD php artisan migrate --force && php -S 0.0.0.0:$PORT -t public