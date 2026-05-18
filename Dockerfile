FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl sqlite3 libsqlite3-dev

RUN docker-php-ext-install pdo pdo_sqlite

COPY . /app

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN touch database/database.sqlite

RUN php artisan key:generate

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
