docker rm -f stack-test-container
git pull
sudo docker build -t stack .
sudo docker run --rm=true -t -i --security-opt seccomp=unconfined -v plots:/var/data/api/stack/plots --name stack-test-container -p 90:80 stack
