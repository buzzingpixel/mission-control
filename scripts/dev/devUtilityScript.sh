#!/usr/bin/env bash

if [[ ! -f /db-volume/ib_buffer_pool ]]; then
    chmod -R 0777 /root/db-starter;
    cp -R /root/db-starter/* /db-volume/;
fi

echo -e "*\n!.gitignore" > /cache-volume/.gitignore;
echo -e "*\n!.gitignore" > /app/cache/.gitignore;

while true; do
    rm -rf /cache-volume/twig/*;
    rm -rf /app/cache/twig/*;

    chmod -R 0777 /cache-volume;
    chmod -R 0777 /db-volume;

    rsync -av /app/vendor/ /vendor-volume --delete
    rsync -av /cache-volume/ /app/cache --delete;
    rsync -av /node-modules-volume/ /app/node_modules --delete;

    sleep 2;
done
