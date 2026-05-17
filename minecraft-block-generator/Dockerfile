FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl unzip zip sqlite3 libsqlite3-dev

RUN docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

CMD ["php-fpm"]