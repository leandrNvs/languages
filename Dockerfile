FROM php:8.2-apache

RUN apt-get update && apt-get install -y zip libzip-dev

RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip
