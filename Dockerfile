FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN composer install

RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]