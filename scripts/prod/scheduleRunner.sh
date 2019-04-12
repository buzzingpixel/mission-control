#!/bin/bash

docker exec -it --user root --workdir /app php-mission-control bash -c "php /app/app schedule/run"
