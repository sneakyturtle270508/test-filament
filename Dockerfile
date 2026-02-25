FROM php:8.2-cli

# Installer system dependencies inkludert oniguruma
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

# Gi rettigheter
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Installer Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Lag storage link for uploads
RUN php artisan storage:link

# Eksponer port
EXPOSE 10000

# Start server og kjør migreringer
CMD php artisan migrate --force && php -S 0.0.0.0:$PORT -t public