@echo off

docker exec -it --user root --workdir /app-volume php-mission-control bash -c "php %*"
