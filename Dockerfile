FROM php:8.2-fpm

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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

RUN composer install --no-dev --optimize-autoloader
RUN php artisan storage:link

EXPOSE 9000

CMD ["php-fpm"]