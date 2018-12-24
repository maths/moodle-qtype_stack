sudo docker run --rm=true\
  -t -i\
  --security-opt seccomp=unconfined \
  -v /var/www/api/plots:/var/data/api/stack/plots:rw  \
  -v /var/www/api/plots:/var/www/html/plots:rw   \
  -v /var/www/api/api:/var/www/html/api  \
  -v /var/www/blob:/var/www/html/api/blob \
  --name stack-api-server \
  -p 90:80 \
  -p 49992:22\
  -p 9000:9000 \
  timimages/stack


  
  
#  -v /opt/tim/timApp/modules/cs/generated/stackplots:/var/data/api/stack/plots:rw  \
