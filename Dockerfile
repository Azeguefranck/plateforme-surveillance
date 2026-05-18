FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl zip sqlite3 libsqlite3-dev libzip-dev

RUN docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p database
RUN touch database/database.sqlite

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

RUN php artisan key:generate
RUN php artisan config:clear

EXPOSE 10000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
