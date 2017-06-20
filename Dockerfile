FROM php:5-apache
RUN mkdir -p /var/data/api
RUN chmod a+rw /var/data/api
RUN mkdir -p /var/data/api/stack/plots &&  mkdir -p /var/data/api/stack/logs && mkdir mkdir -p /var/data/api/stack/tmp
RUN apt-get update && apt-get install maxima gnuplot -y
RUN pecl install xdebug-2.5.0 && docker-php-ext-enable xdebug
COPY ./api/config.php.docker /var/www/html/config.php
COPY ./ /var/www/html
RUN php /var/www/html/api/install.php
RUN chmod -R a+rw /var/data/api/stack
