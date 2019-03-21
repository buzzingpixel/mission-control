@echo off

type nul >> .env.override
docker-compose up -d
docker exec -it --user root php-mission-control bash -c "chmod +x /app/scripts/dev/ensureComposerInstall.sh && /app/scripts/dev/ensureComposerInstall.sh"
docker exec -it --user root --workdir /app php-mission-control bash -c "php app migrate/up"
docker exec -it --user root --workdir /app php-mission-control bash -c "php app seed/run"
docker exec -it --user root --workdir /app node-mission-control bash -c "yarn"
