#!/usr/bin/env bash

if [[ ! -f /app/node_modules/npm-installed ]]; then
    cd /app;
    npm install;
    touch /app/node_modules/npm-installed
fi
