#!/usr/bin/env bash

if [[ ! -f /db-volume/ib_buffer_pool ]]; then
    echo 'Adding DB starter pack to /var/lib/mysql' >> /app/dev/null/msg.txt;
    chmod -R 0777 /root/db-starter;
    cp -R /root/db-starter/* /db-volume/;
fi

while true; do
    rm -rf /app/cache/twig/*;
    chmod -R 0777 /app/cache;
    chmod -R 0777 /db-volume;
    sleep 5;
done
