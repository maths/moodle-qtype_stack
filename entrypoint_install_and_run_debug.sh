service ssh start
php /var/www/html/api/install.php
echo 'zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so\n\
[XDebug]\n\
xdebug.remote_host = 192.168.59.6\n\
xdebug.remote_enable = 1\n\
xdebug.remote_autostart = 1\n' >>/usr/local/etc/php/php.ini

sed -i '3ichmod a+rwx /var/data/api/stack /var/data/api/stack/logs /var/data/api/stack/tmp /var/data/api/stack/plots' /usr/local/bin/docker-php-entrypoint
echo "<?php phpinfo(); ?>" >phpinfo.php
. /etc/apache2/envvars
apache2 -D FOREGROUND
