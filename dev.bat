@echo off

set cmd=%1
set allArgs=%*
for /f "tokens=1,* delims= " %%a in ("%*") do set allArgsExceptFirst=%%b
set secondArg=%2
set valid=false

if "%cmd%" == "" (
    set valid=true
    echo The following commands are available:
    echo   .\dev up
    echo   .\dev watch
    echo   .\dev build
    echo   .\dev down
    echo   .\dev phpunit [args]
    echo   .\dev yarn [args]
    echo   .\dev queue
    echo   .\dev cli [args]
    echo   .\dev composer [args]
    echo   .\dev login [args]
)

if "%1%" == "up" (
    set valid=true
    type nul >> .env.override
        docker kill node-mission-control
        docker-compose -f docker-compose.yml -p mission-control up -d
        docker exec -it --user root --workdir /app php-mission-control bash -c "cd /app && composer install"
        docker exec -it --user root --workdir /app php-mission-control bash -c "chmod +x /app/vendor/bin/*"
        docker exec -it --user root --workdir /app php-mission-control bash -c "chmod +x /app/vendor/phpmd/phpmd/src/bin/*"
        docker exec -it --user root --workdir /app php-mission-control bash -c "chmod +x /app/vendor/squizlabs/php_codesniffer/bin/*"
        docker exec -it --user root --workdir /app php-mission-control bash -c "php app migrate/up"
        docker exec -it --user root --workdir /app php-mission-control bash -c "php app seed/run"
        docker exec -it --user root --workdir /app node-mission-control bash -c "yarn"
        docker exec -it --user root --workdir /app node-mission-control bash -c "yarn run fab --build-only"
)

if "%1%" == "watch" (
    set valid=true
    docker exec -it --user root --workdir /app node-mission-control bash -c "yarn run fab"
)

if "%1%" == "build" (
    set valid=true
    docker exec -it --user root --workdir /app node-mission-control bash -c "yarn run fab --build-only"
)

if "%1%" == "down" (
    set valid=true
    docker-compose -f docker-compose.yml -p mission-control down
)

if "%1%" == "phpunit" (
    set valid=true
    docker exec -it --user root --workdir /app php-mission-control bash -c "chmod +x /app/vendor/bin/phpunit && /app/vendor/bin/phpunit --configuration /app/phpunit.xml %allArgsExceptFirst%"
)

if "%1%" == "yarn" (
    set valid=true
    docker kill node-mission-control
    docker-compose -f docker-compose.yml -p mission-control up -d
    docker exec -it --user root --workdir /app node-mission-control bash -c "%allArgs%"
)

if "%1%" == "queue" (
    set valid=true
    docker exec -it --user root --workdir /app-www php-mission-control bash -c "chmod +x /app/scripts/dev/queueRunner.sh && /app/scripts/dev/queueRunner.sh"
)

if "%1%" == "cli" (
    set valid=true
    docker exec -it --user root --workdir /app-www php-mission-control bash -c "php app %allArgsExceptFirst%"
)

if "%1%" == "composer" (
    set valid=true
    docker exec -it --user root --workdir /app php-mission-control bash -c "%allArgs%"
)

if "%1%" == "login" (
    set valid=true
    docker exec -it --user root %secondArg%-mission-control bash
)

if not "%valid%" == "true" (
    echo Specified command not found
    exit /b 1
)

exit /b 0
