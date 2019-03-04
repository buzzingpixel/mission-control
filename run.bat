@echo off

docker kill node-mission-control
docker-compose up -d
docker exec -it --user root php-mission-control bash -c "chmod +x /app/scripts/ensureComposerInstall.sh && /app/scripts/ensureComposerInstall.sh"
docker exec -it --user root node-mission-control bash -c "chmod +x /app/scripts/ensureNpmInstall.sh && /app/scripts/ensureNpmInstall.sh"
docker exec -it --user root --workdir /app node-mission-control bash -c "npm run fab"
