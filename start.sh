docker rm -f stack-api-server
git pull
sudo docker build -t stack .
sudo docker run --rm=true -t -i --security-opt seccomp=unconfined -v plots:/var/data/api/stack/plots --name stack-api-server -p 90:80 stack
