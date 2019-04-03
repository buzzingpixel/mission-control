<?php
declare(strict_types=1);

// phpcs:ignoreFile

use corbomite\di\Di;
use Symfony\Component\Dotenv\Dotenv;
use corbomite\cli\Kernel as CliKernel;
use corbomite\http\Kernel as HttpKernel;
use src\app\http\middlewares\ErrorPagesMiddleware;

@session_start();

define('APP_BASE_PATH', __DIR__);
define('APP_VENDOR_PATH', APP_BASE_PATH . '/vendor');
putenv('TWIG_CACHE_PATH=' . APP_BASE_PATH . '/cache/twig');
putenv('CORBOMITE_DB_DATA_NAMESPACE=src\app\data');
putenv('CORBOMITE_DB_DATA_DIRECTORY=./src/app/data');

require APP_VENDOR_PATH . '/autoload.php';

if (class_exists(Dotenv::class)) {
    $dotenv = new Dotenv();
    $dotenv->overload(__DIR__ . '/.env');
    $optionalFile = __DIR__ . '/.env.override';
    if (file_exists($optionalFile)) {
        $dotenv->overload($optionalFile);
    }
}

if (! getenv('SITE_URL')) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $protocol = $secure ? 'https://' : 'http://';
    putenv('SITE_URL=' . $protocol . $_SERVER['HTTP_HOST']);
}

putenv('SITE_URL=' . rtrim(getenv('SITE_URL'), '/'));

if (PHP_SAPI === 'cli') {
    require __DIR__ . '/src/config/devMode.php';

    /** @noinspection PhpUnhandledExceptionInspection */
    Di::diContainer()->get(CliKernel::class)($argv);
    exit();
}

if (getenv('DEV_MODE') === 'true') {
    require __DIR__ . '/src/config/devMode.php';
}

/** @noinspection PhpUnhandledExceptionInspection */
Di::diContainer()->get(HttpKernel::class)(ErrorPagesMiddleware::class);
exit();
