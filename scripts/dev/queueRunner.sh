#!/bin/bash

while true; do
    php /app/app queue/run;
    sleep 0.1;
done
