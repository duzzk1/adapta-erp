FROM php:8.4-fpm

# System dependencies
RUN apt-get update \
    && apt-get install -y \
       git \
       unzip \
       libzip-dev \
       libpng-dev \
       libonig-dev \
       libxml2-dev \
     && docker-php-ext-install \
       pdo_mysql \
       mbstring \
       zip \
       exif \
         gd \
         opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Ensure storage and bootstrap/cache are writable (on first build)
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# PHP Opcache recommended settings for dev (fast reload)
COPY php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Default command runs php-fpm
CMD ["php-fpm"]
