@echo off

call up.bat
docker exec -it --user root --workdir /app-www php-mission-control bash -c "chmod +x /app/scripts/dev/queueRunner.sh && /app/scripts/dev/queueRunner.sh"
