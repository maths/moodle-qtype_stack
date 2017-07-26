FROM php:5-apache
RUN mkdir -p /var/data/api
RUN chmod a+rw /var/data/api
RUN mkdir -p /var/data/api/stack/plots &&  mkdir -p /var/data/api/stack/logs && mkdir mkdir -p /var/data/api/stack/tmp
RUN apt-get update && apt-get install maxima gnuplot -y
# RUN pecl install xdebug-2.5.0 && docker-php-ext-enable xdebug
COPY ./ /var/www/html
COPY ./api/config.php.docker /var/www/html/config.php
RUN php /var/www/html/api/install.php
RUN chmod -R a+rwx /var/data/api/stack
#RUN sed -i "s/maximacommand.*$/maximacommand = 'timeout --kill-after=10s 10s \/var\/data\/api\/stack\/maxima_opt_auto -eval \\\'(cl-user::run)\\\'';/" /var/www/html/config.php
#RUN sed -i "s/platform.*$/platform = 'unix-optimised';/" /var/www/html/config.php
