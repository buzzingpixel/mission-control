#!/usr/bin/env bash

if [[ ! -f /db-volume/ib_buffer_pool ]]; then
    chmod -R 0777 /root/db-starter;
    cp -R /root/db-starter/* /db-volume/;
    chmod -R 0777 /db-volume;
fi

if [[ ! -f /db-backups-volume/ib_buffer_pool ]]; then
    chmod -R 0777 /root/db-starter;
    cp -R /root/db-starter/* /db-backups-volume/;
    chmod -R 0777 /db-backups-volume;
fi

while true; do
    chmod -R 0777 /cache-volume;
    chmod -R 0777 /log-volume;
    sleep 2;
done
