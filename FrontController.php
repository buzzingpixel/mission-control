<?php
declare(strict_types=1);

// phpcs:ignoreFile

use corbomite\di\Di;
use corbomite\cli\Kernel as CliKernel;
use corbomite\http\Kernel as HttpKernel;
use src\app\http\middlewares\ErrorPagesMiddleware;

define('APP_BASE_PATH', __DIR__);
define('APP_VENDOR_PATH', APP_BASE_PATH . '/vendor');
putenv('TWIG_CACHE_PATH=' . APP_BASE_PATH . '/cache/twig');
putenv('CORBOMITE_DB_DATA_NAMESPACE=src\app\data');
putenv('CORBOMITE_DB_DATA_DIRECTORY=./src/app/data');

require APP_VENDOR_PATH . '/autoload.php';

if (file_exists(APP_BASE_PATH . '/.env')) {
    (new Dotenv\Dotenv(APP_BASE_PATH))->load();
}

if (getenv('DISABLE_CSRF') === 'true') {
    define('CSRF_EXEMPT_SEGMENTS', [
        '',
        'admin',
        'account',
        'projects',
        'monitored-urls',
        'pings',
        'reminders',
    ]);
}

if (! getenv('SITE_URL')) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $protocol = $secure ? 'https://' : 'http://';
    putenv('SITE_URL=' . $protocol . $_SERVER['HTTP_HOST']);
}

putenv('SITE_URL=' . rtrim(getenv('SITE_URL'), '/'));

if (PHP_SAPI === 'cli') {
    // Register handler to catch errors that come up before the app registers a handler
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
    $whoops->register();

    /** @noinspection PhpUnhandledExceptionInspection */
    Di::get(CliKernel::class)($argv);
    exit();
}

if (getenv('DEV_MODE') === 'true') {
    // Register handler to catch errors that come up before Yii registers a handler
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();

    // Configure ref dumper
    /** @noinspection PhpUnhandledExceptionInspection */
    ref::config('shortcutFunc', ['r', 'rt', 'd', 'dd']);

    function d()
    {
        call_user_func_array('r', func_get_args());
    }

    function dd()
    {
        ob_clean();
        call_user_func_array('r', func_get_args());
        die;
    }
}

/** @noinspection PhpUnhandledExceptionInspection */
Di::get(HttpKernel::class)(ErrorPagesMiddleware::class);
exit();
