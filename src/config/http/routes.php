<?php
declare(strict_types=1);

/**
 * @see https://github.com/nikic/FastRoute
 */

/** @var \FastRoute\RouteCollector $routeCollector */

use src\app\http\controllers\IndexController;
use src\app\http\controllers\ForgotPasswordController;

$routeCollector->addRoute(['GET', 'POST'], '/', IndexController::class);
$routeCollector->get('/iforgot', ForgotPasswordController::class);
