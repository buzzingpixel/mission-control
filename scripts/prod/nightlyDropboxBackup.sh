#!/bin/bash

docker exec --user root --workdir /app backups-mission-control bash -c "/root/dropboxserverbackups/backup.sh"
