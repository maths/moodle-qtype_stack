php /var/www/html/api/install.php
sed -i '3ichmod a+rwx /var/data/api/stack /var/data/api/stack/logs /var/data/api/stack/tmp /var/data/api/stack/plots' /usr/local/bin/docker-php-entrypoint
. /etc/apache2/envvars
apache2 -D FOREGROUND
