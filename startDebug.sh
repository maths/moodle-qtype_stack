sudo docker run --rm=true\
  -t -i\
  --security-opt seccomp=unconfined \
  -v /var/www/api/plots:/var/data/api/stack/plots:rw  \
  -v /var/www/api/plots:/var/www/html/plots:rw   \
  -v /var/www/api/api:/var/www/html/api  \
  -v /var/www/blob:/var/www/html/api/blob \
  -v /var/www/api/api/config.php.docker:/var/www/html/config.php \
  --name tim_stack-api-server_1 \
  -p 90:80 \
  -p 49992:22\
  -p 9000:9000 \
  timimages/stack-debug


  
  
#  -v /opt/tim/timApp/modules/cs/generated/stackplots:/var/data/api/stack/plots:rw  \
