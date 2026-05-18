FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev

RUN docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --ignore-platform-reqs

RUN mkdir -p database
RUN touch database/database.sqlite

ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/app/database/database.sqlite

RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan key:generate || true

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
