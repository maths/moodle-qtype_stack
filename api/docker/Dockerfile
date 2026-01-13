#Intermediate step installing dependencies
FROM composer:lts as composer

COPY / /work/

WORKDIR /work/api/

RUN composer install
RUN composer dump-autoload --optimize

#Base Image
FROM php:8.4.5-apache-bookworm AS base

#Install required php extensions
RUN apt-get update && \
    apt-get -y install libicu-dev libyaml-dev libzip-dev zip && \
    pecl install yaml && \
    docker-php-ext-configure intl && \
    docker-php-ext-enable yaml && \
    docker-php-ext-install intl zip && \
    a2enmod rewrite && a2enmod actions

ENV APACHE_DOCUMENT_ROOT /srv/stack/api/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY ./api/docker/files/000-default.conf /etc/apache2/sites-available/000-default.conf

#Create directories for stack
RUN mkdir -p /var/data/api && \
    mkdir /var/data/api/stack && \
    mkdir /var/data/api/stack/plots && \
    mkdir /var/data/api/stack/logs && \
    mkdir /var/data/api/stack/tmp && \
    chmod -R 777 /var/data/api

#Copy Source Code
COPY / /srv/stack

#Copy dependencies
COPY --from=composer /work/api/vendor /srv/stack/api/vendor

FROM base as development

#Enable xdebug extension in develop
COPY /api/docker/files/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

FROM development as profiling

COPY /api/docker/files/xdebug-profile.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM base as production

#Enable opcache extension in production
COPY /api/docker/files/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN docker-php-ext-install opcache && \
    docker-php-ext-enable opcache

# Required for building the Edinburgh demo server
#RUN a2enmod ssl
#COPY ./api/docker/files/000-default-ssl.conf /etc/apache2/sites-available/000-default-ssl.conf
#RUN apt install python3 python3-dev python3-venv libaugeas-dev gcc && \
#    python3 -m venv /opt/certbot/ && \
#    /opt/certbot/bin/pip install --upgrade pip && \
#    /opt/certbot/bin/pip install certbot certbot-apache && \
#    echo "0 0,12 * * * root /opt/certbot/bin/python -c 'import random; import time; time.sleep(random.random() * 3600)' && sudo certbot renew -q" | sudo tee -a /etc/crontab > /dev/null