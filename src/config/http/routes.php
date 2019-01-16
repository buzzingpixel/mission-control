<?php
declare(strict_types=1);

/**
 * @see https://github.com/nikic/FastRoute
 */

/** @var \FastRoute\RouteCollector $routeCollector */

use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\ForgotPasswordController;

$routeCollector->addRoute(['GET', 'POST'], '/', ProjectsIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects', ProjectsIndexController::class);

// Password Reset routes
$routeCollector->get('/iforgot', ForgotPasswordController::class);
$routeCollector->get('/iforgot/check-email', ForgotPasswordController::class);
$routeCollector->get('/iforgot/reset/{token}', PasswordResetController::class);
