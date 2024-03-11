FROM php:fpm-alpine

LABEL maintainer="nguyennv1981@gmail.com"

RUN docker-php-ext-configure opcache --enable-opcache \
    && apk add icu-dev \
    && pecl install redis \
    && docker-php-ext-install intl mysqli pdo_mysql \
    && docker-php-ext-enable intl mysqli pdo_mysql opcache redis \
    && docker-php-source delete

WORKDIR /var/www/html
