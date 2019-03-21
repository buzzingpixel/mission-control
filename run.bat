@echo off

docker kill node-mission-control
call up.bat
docker exec -it --user root --workdir /app node-mission-control bash -c "yarn run fab"
