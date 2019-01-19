#!/usr/bin/env bash

while true; do
    rm -rf /app/cache/twig/*;
    chmod -R 0777 /app/cache;
    chmod -R 0777 /var/lib/mysql;
    chmod -R 0777 /app/public/cache;
    rsync -av /app/vendor/ /vendor-volume --delete
    sleep 2;
done
