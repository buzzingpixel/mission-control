#!/usr/bin/env bash

if [[ ! -f /app/vendor/composer-installed ]]; then
    cd /app;
    composer install;
    touch /app/vendor/composer-installed
fi

chmod +x /app/vendor/bin/*;
chmod +x /app/vendor/phpmd/phpmd/src/bin/*;
chmod +x /app/vendor/squizlabs/php_codesniffer/bin/*;
