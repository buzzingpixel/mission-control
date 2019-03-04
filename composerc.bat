@echo off

docker exec -it --user root --workdir /app php-mission-control bash -c "composer %*"
