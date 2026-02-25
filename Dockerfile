FROM php:8.2-fpm

# Installer system dependencies
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
    build-essential \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip tokenizer

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Kopier prosjektfiler
COPY . .

# Rettigheter
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Installer Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Lag storage link
RUN php artisan storage:link

# Eksponer port
EXPOSE 9000

# Start FPM server
CMD ["php-fpm"]