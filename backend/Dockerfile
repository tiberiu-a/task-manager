FROM composer:2 AS composer_bin

FROM php:8.3-cli-alpine
RUN docker-php-ext-install pdo_mysql
COPY --from=composer_bin /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-interaction

COPY . .

EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:8000", "-t", "public" ]

