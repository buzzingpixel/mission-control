@echo off

docker kill node-mission-control
docker-compose up -d
docker exec -it --user root --workdir /app node-mission-control bash -c "yarn %*"
