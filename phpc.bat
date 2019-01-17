@echo off

docker-compose up -d
docker exec -it --user root --workdir /app php-mission-control bash -c "php %*"
