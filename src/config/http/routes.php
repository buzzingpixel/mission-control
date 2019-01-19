<?php
declare(strict_types=1);

/**
 * @see https://github.com/nikic/FastRoute
 */

/** @var FastRoute\RouteCollector $routeCollector */

use src\app\http\controllers\AccountController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\CreateMonitoredUrlController;

// Monitored URLS
$routeCollector->addRoute(['GET', 'POST'], '/account', AccountController::class);

// Projects
$routeCollector->addRoute(['GET', 'POST'], '/', ProjectsIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects[/{archives:archives}]', ProjectsIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects/create', CreateProjectController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects/view/{slug}', ViewProjectController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects/edit/{slug}', EditProjectController::class);

// Monitored URLS
$routeCollector->addRoute(['GET', 'POST'], '/monitored-urls[/{archives:archives}]', MonitoredUrlIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/monitored-urls/create', CreateMonitoredUrlController::class);

// Password Reset routes
$routeCollector->get('/iforgot', ForgotPasswordController::class);
$routeCollector->get('/iforgot/check-email', ForgotPasswordController::class);
$routeCollector->get('/iforgot/reset/{token}', PasswordResetController::class);
