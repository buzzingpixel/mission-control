@echo off

docker exec -it --user root --workdir /app-www php-mission-control bash -c "php %*"
