FROM php:alpine

LABEL maintainer="nguyennv1981@gmail.com"

RUN docker-php-ext-configure opcache --enable-opcache \
    && pecl install redis \
    && docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-enable mysqli pdo_mysql opcache redis \
    && docker-php-source delete

WORKDIR /var/www/html
