FROM php:8.4-cli-alpine

WORKDIR /var/www
RUN apk add --no-cache git unzip libzip-dev libxml2-dev oniguruma-dev sqlite-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip pcntl xml \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
EXPOSE 8000
