@echo off

docker-compose up -d
docker exec -it --user root --workdir /app php-mission-control bash -c "chmod +x /app/scripts/queueRunner.sh && /app/scripts/queueRunner.sh"
