#!/bin/bash

# Run the queue every second infinitely
while true; do
    php /app/app queue/run;
    sleep 1;
done
