FROM php:8.2-apache

RUN apt update && apt install -y \
    git \
    libzip-dev

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN a2enmod rewrite

RUN useradd -s /bin/bash php

USER php