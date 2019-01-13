<?php
declare(strict_types=1);

/** @var \FastRoute\RouteCollector $routeCollector */

use src\app\http\controllers\IndexController;

$routeCollector->get('/', IndexController::class);
