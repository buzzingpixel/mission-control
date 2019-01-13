#!/usr/bin/env bash

COUNTER=1;

while true; do
    chmod -R 0777 /app/cache;
    chmod -R 0777 /var/lib/mysql;
    echo ${COUNTER} > /app/dev/null/tmp;
    rsync -av /app/vendor/ /vendor-volume --delete
    COUNTER=$((COUNTER+1));
    sleep 2;
done
