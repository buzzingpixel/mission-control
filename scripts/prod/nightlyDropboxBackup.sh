#!/bin/bash

docker exec --user root --workdir /app utility-mission-control bash -c "cp -R /db-volume/* /db-mirror-volume/;"
docker exec --user root --workdir /app backups-mission-control bash -c "/root/dropboxserverbackups/backup.sh"
