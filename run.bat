@echo off

docker kill node-mission-control
call up.bat
docker exec -it --user root node-mission-control bash -c "chmod +x /app/scripts/ensureNpmInstall.sh && /app/scripts/ensureNpmInstall.sh"
docker exec -it --user root --workdir /app node-mission-control bash -c "npm run fab"
