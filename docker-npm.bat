@echo off

docker exec -it --user root --workdir /app node-mission-control bash -c "npm %*"
