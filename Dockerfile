FROM php:7.0-apache
RUN mkdir -p /var/data/api
RUN chmod a+rw /var/data/api
RUN mkdir -p /var/data/api/stack/logs && mkdir mkdir -p /var/data/api/stack/tmp
#
# The next line does not actually install a working maxima!
# We probably need to look at
# https://github.com/uni-halle/maximapool-docker/blob/develop/Dockerfile
# 
RUN apt-get update && apt-get install maxima gnuplot libyaml-dev unzip git -y
RUN pecl install yaml
RUN echo "extension=yaml.so" > /usr/local/etc/php/conf.d/yaml.ini
#RUN pecl install xdebug-2.5.0 && docker-php-ext-enable xdebug
#RUN echo 'xdebug.profiler_enable=1\nxdebug.profiler_output_dir=/tmp\nxdebug.profiler_output_name = "cachegrind.out.%t-%s"\nxdebug.profiler_append=0\n' > /usr/local/etc/php/conf.d/xdebug.ini
VOLUME ["/var/data/api/stack/plots"]
RUN ln -s /var/data/api/stack/plots /var/www/html/plots
COPY ./ /var/www/html
COPY ./api/config.php.docker /var/www/html/config.php
RUN sed s/stack_cas_castext_jsxgraphapi/stack_cas_castext_jsxgraph/  /var/www/html/stack/cas/castext/jsxgraphapi.block.php > /var/www/html/stack/cas/castext/jsxgraph.block.php
RUN chmod a+rwx /var/data/api/stack /var/data/api/stack/logs /var/data/api/stack/tmp /var/data/api/stack/plots
# RUN maxima --version
# RUN php /var/www/html/api/install.php
#RUN sed -i "s/maximacommand.*$/maximacommand = 'timeout --kill-after=10s 10s \/var\/data\/api\/stack\/maxima_opt_auto -eval \\\'(cl-user::run)\\\'';/" /var/www/html/config.php
#RUN sed -i "s/platform.*$/platform = 'unix-optimised';/" /var/www/html/config.php
# RUN sed -i '3ichmod a+rwx /var/data/api/stack /var/data/api/stack/logs /var/data/api/stack/tmp /var/data/api/stack/plots' /usr/local/bin/docker-php-entrypoint

ENTRYPOINT  /var/www/html/entrypoint_install_and_run.sh 
