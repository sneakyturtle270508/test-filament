# Bruk PHP 8.3 med FPM
FROM php:8.3-cli

# Installer system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libxml2-dev zlib1g-dev libzip-dev libonig-dev pkg-config build-essential autoconf bison libicu-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip tokenizer intl

# Sett working directory
WORKDIR /var/www

# Kopier prosjektfiler
COPY . /var/www

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Kjør Composer install uten dev
RUN composer install --no-dev --optimize-autoloader

# Lag storage link
RUN php artisan storage:link

# Lag start script
COPY start.sh /var/www/start.sh
RUN chmod +x /var/www/start.sh

# Eksponer port for Render
EXPOSE 10000

# CMD til entrypoint
CMD ["./start.sh"]