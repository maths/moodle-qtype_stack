#Build ARG used to switch between development and production versions
ARG ENVIRONMENT=production

#Intermediate step installing dependencies
FROM composer as composer

WORKDIR /work

COPY ./api/composer.* /work
RUN composer install

#Base Image
FROM php:8.2-apache AS base

RUN apt-get update -y && \
    apt-get install -y libicu-dev libyaml-dev libzip-dev zip && \
    pecl install yaml && \
    docker-php-ext-configure intl && \
    docker-php-ext-enable yaml && \
    docker-php-ext-install intl zip && \
    a2enmod rewrite && a2enmod actions

ENV APACHE_DOCUMENT_ROOT /srv/stack/api/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN mkdir -p /var/data/api && \
    mkdir /var/data/api/stack && \
    mkdir /var/data/api/stack/plots && \
    mkdir /var/data/api/stack/logs && \
    mkdir /var/data/api/stack/tmp && \
    chmod -R 777 /var/data/api

COPY ./api/docker/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY ./ /srv/stack
COPY --from=composer /work/vendor /srv/stack/api/vendor

FROM base as production

FROM base as development

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

COPY ./api/docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM ${ENVIRONMENT} as final
