#!/bin/bash

while true; do
    php /app/app queue/run;
    sleep 1;
done
