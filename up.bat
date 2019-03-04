@echo off

type nul >> .env.override
docker-compose up -d
docker exec -it --user root php-mission-control bash -c "chmod +x /app/scripts/ensureComposerInstall.sh && /app/scripts/ensureComposerInstall.sh"
