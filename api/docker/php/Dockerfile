#Intermediate step installing dependencies
FROM composer as composer

COPY / /work/

WORKDIR /work/api/

RUN composer install
RUN composer dump-autoload --optimize

#Base Image
FROM php:8.2-fpm-alpine AS base

#Install required php extensions
RUN apk add g++ make autoconf libzip icu yaml icu-dev yaml-dev libzip-dev && \
    pecl install yaml && \
    docker-php-ext-configure intl && \
    docker-php-ext-enable yaml && \
    docker-php-ext-install intl zip && \
    apk del g++ make autoconf icu-dev yaml-dev libzip-dev

#Create directories for stack
RUN mkdir -p /var/data/api && \
    mkdir /var/data/api/stack && \
    mkdir /var/data/api/stack/plots && \
    mkdir /var/data/api/stack/logs && \
    mkdir /var/data/api/stack/tmp && \
    chmod -R 777 /var/data/api

#Copy Source Code
COPY --chown=www-data:www-data / /srv/stack

#Copy dependencies
COPY --from=composer --chown=www-data:www-data /work/api/vendor /srv/stack/api/vendor
COPY /api/docker/php/files/www.conf /usr/local/etc/php-fpm.d/www.conf

FROM base as development

#Enable xdebug extension in develop
COPY /api/docker/php/files/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN apk add g++ make autoconf linux-headers && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

FROM base as production

#Enable opcache extension in production
COPY /api/docker/php/files/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN docker-php-ext-install opcache && \
    docker-php-ext-enable opcache
