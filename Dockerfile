FROM php:8.3-fpm

# System dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libxml2-dev zlib1g-dev libzip-dev libonig-dev pkg-config build-essential autoconf bison libicu-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Safe directory for git
RUN git config --global --add safe.directory /var/www

# Copy project
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Storage link
RUN php artisan storage:link

# Expose port for Render
EXPOSE 10000

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]