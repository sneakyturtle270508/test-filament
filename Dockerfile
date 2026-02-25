# Bruk PHP 8.3 CLI med alle dev headers
FROM php:8.3-cli

# Sett working directory
WORKDIR /var/www

# Installer system dependencies
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libxml2-dev zlib1g-dev libzip-dev libonig-dev pkg-config build-essential autoconf bison libicu-dev libsqlite3-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip tokenizer intl

# Kopier Composer bin fra offisiell image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Kopier prosjektfiler
COPY . /var/www

# Composer install uten dev
RUN composer install --no-dev --optimize-autoloader

# Lag storage symlink
RUN php artisan storage:link

# Lag start script
COPY start.sh /var/www/start.sh
RUN chmod +x /var/www/start.sh

# Eksponer port for Render
EXPOSE 10000

# CMD til entrypoint
CMD ["./start.sh"]