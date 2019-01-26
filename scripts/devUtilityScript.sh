#!/usr/bin/env bash

if [[ ! -f /db-volume/ib_buffer_pool ]]; then
    echo 'Adding DB starter pack to /var/lib/mysql' >> /app/dev/null/msg.txt;
    chmod -R 0777 /root/db-starter;
    cp -R /root/db-starter/* /db-volume/;
fi

while true; do
    rm -rf /app/cache/twig/*;
    chmod -R 0777 /cache-volume;
    chmod -R 0777 /db-volume;
    chmod -R 0777 /public-cache-volume;
    rsync -av /app/vendor/ /vendor-volume --delete
    # rsync -av /cache-volume/ /app/cache --delete;
    sleep 2;
done
