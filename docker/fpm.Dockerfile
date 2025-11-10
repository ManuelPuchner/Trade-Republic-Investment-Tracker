FROM php:8.3-fpm-alpine AS base
ENV EXT_APCU_VERSION=master
RUN curl -vvv https://github.com/krakjoe/apcu.git

RUN apk add --update zlib-dev libpng-dev libzip-dev libxml2-dev icu-dev postgresql-dev $PHPIZE_DEPS oniguruma-dev

RUN docker-php-ext-install exif
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install xml
RUN docker-php-ext-install intl
# RUN pecl install apcu
RUN docker-php-source extract \
    && apk -Uu add git \
    && git clone --branch $EXT_APCU_VERSION --depth 1 https://github.com/krakjoe/apcu.git /usr/src/php/ext/apcu \
    && cd /usr/src/php/ext/apcu && git submodule update --init \
    && docker-php-ext-install apcu
RUN docker-php-ext-enable apcu

FROM node:20-alpine AS assets-build
WORKDIR /var/www/html
COPY . /var/www/html/

RUN apk add --no-cache php php-cli php-dom php-intl php-session php-fileinfo php-tokenizer php-xml php-mbstring php-xmlreader php-gd php-simplexml php-xmlwriter composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Assets are pre-built by GitHub Actions, check what directories exist
RUN ls -la /var/www/html/ && ls -la /var/www/html/public/ || true

FROM base AS dev

COPY /composer.json composer.json
COPY /composer.lock composer.lock
COPY /app app
COPY /bootstrap bootstrap
COPY /config config
COPY /artisan artisan

FROM base AS build-fpm

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY /artisan artisan
COPY . /var/www/html
COPY --from=assets-build /var/www/html/public/build /var/www/html/public/build
# COPY /composer.json composer.json

RUN composer install --optimize-autoloader --no-scripts

COPY /bootstrap bootstrap
COPY /app app
COPY /config config
COPY /routes routes


# COPY . /var/www/html

RUN composer dump-autoload -o --no-scripts

FROM build-fpm AS fpm

COPY --from=build-fpm /var/www/html /var/www/html
# Build directory should already be in public folder from assets-build stage