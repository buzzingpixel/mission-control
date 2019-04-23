@echo off

set cmd=%1
set allArgs=%*
for /f "tokens=1,* delims= " %%a in ("%*") do set allArgsExceptFirst=%%b
set secondArg=%2
set valid=false

:: If no command provided, list commands
if "%cmd%" == "" (
    set valid=true
    echo The following commands are available:
    echo   .\dev up
    echo   .\dev run
    echo   .\dev down
    echo   .\dev phpunit [args]
    echo   .\dev yarn [args]
    echo   .\dev queue
    echo   .\dev cli [args]
    echo   .\dev composer [args]
    echo   .\dev login [args]
)

:: If command is up or run, we need to run the docker containers and install composer and yarn dependencies
if "%1%" == "up" (
    set valid=true
    call up
)

:: If the command is run, then we want to run the build process and watch for changes
if "%1%" == "run" (
    set valid=true
    call up
    docker exec -it --user root --workdir /app node-mission-control bash -c "yarn run fab"
)

:: If the command is down, then we want to stop docker
if "%1%" == "down" (
    set valid=true
    docker-compose -f docker-compose.yml -p mission-control down
)

:: Run phpunit if requested
if "%1%" == "phpunit" (
    set valid=true
    docker exec -it --user root --workdir /app php-mission-control bash -c "chmod +x /app/vendor/bin/phpunit && /app/vendor/bin/phpunit --configuration /app/phpunit.xml %allArgsExceptFirst%"
)

:: Run yarn if requested
if "%1%" == "yarn" (
    set valid=true
    docker kill node-mission-control
    docker-compose -f docker-compose.yml -p mission-control up -d
    docker exec -it --user root --workdir /app node-mission-control bash -c "%allArgs%"
)

:: Run queue if requested
if "%1%" == "queue" (
    set valid=true
    docker exec -it --user root --workdir /app-www php-mission-control bash -c "chmod +x /app/scripts/dev/queueRunner.sh && /app/scripts/dev/queueRunner.sh"
)

:: Run cli if requested
if "%1%" == "cli" (
    set valid=true
    docker exec -it --user root --workdir /app-www php-mission-control bash -c "php app %allArgsExceptFirst%"
)

:: Run composer if requested
if "%1%" == "composer" (
    set valid=true
    docker exec -it --user root --workdir /app php-mission-control bash -c "%allArgs%"
)

:: Login to a container if requested
if "%1%" == "login" (
    set valid=true
    docker exec -it --user root %secondArg%-mission-control bash
)

:: If there was no valid command found, warn user
if not "%valid%" == "true" (
    echo Specified command not found
    exit /b 1
)

:: Exit with no error
exit /b 0

:: Up function
:up
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

    if not "%1%" == "run" (
        docker exec -it --user root --workdir /app node-mission-control bash -c "yarn run fab --build-only"
    )
exit /b 0
