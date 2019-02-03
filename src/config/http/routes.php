<?php
declare(strict_types=1);

/**
 * @see https://github.com/nikic/FastRoute
 */

/** @var \FastRoute\RouteCollector $routeCollector */

use src\app\http\controllers\TestController;
use src\app\http\controllers\AdminController;
use src\app\http\controllers\AccountController;
use src\app\http\controllers\EditPingController;
use src\app\http\controllers\ViewPingController;
use src\app\http\controllers\PingIndexController;
use src\app\http\controllers\CreatePingController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\PingCheckinController;
use src\app\http\controllers\ServersIndexController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\ViewReminderController;
use src\app\http\controllers\EditReminderController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\ChangePasswordController;
use src\app\http\controllers\CreateReminderController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\RemindersIndexController;
use src\app\http\controllers\EditMonitoredUrlController;
use src\app\http\controllers\ViewMonitoredUrlController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\CreateMonitoredUrlController;
use src\app\http\controllers\AddNotificationEmailController;

// Testing
if (getenv('DEV_MODE') === 'true') {
    $routeCollector->addRoute(['GET', 'POST'], '/test', new TestController());
}

// Account
$routeCollector->addRoute(['GET', 'POST'], '/account', AccountController::class);
$routeCollector->addRoute(['GET', 'POST'], '/account/change-password', ChangePasswordController::class);

// Projects
$routeCollector->addRoute(['GET', 'POST'], '/', ProjectsIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects[/{archives:archives}]', ProjectsIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects/create', CreateProjectController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects/view/{slug}', ViewProjectController::class);
$routeCollector->addRoute(['GET', 'POST'], '/projects/edit/{slug}', EditProjectController::class);

// Monitored URLS
$routeCollector->addRoute(['GET', 'POST'], '/monitored-urls[/{archives:archives}]', MonitoredUrlIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/monitored-urls/create', CreateMonitoredUrlController::class);
$routeCollector->addRoute(['GET', 'POST'], '/monitored-urls/view/{slug}', ViewMonitoredUrlController::class);
$routeCollector->addRoute(['GET', 'POST'], '/monitored-urls/edit/{slug}', EditMonitoredUrlController::class);

// Pings
$routeCollector->addRoute(['GET', 'POST'], '/pings[/{archives:archives}]', PingIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/pings/create', CreatePingController::class);
$routeCollector->addRoute(['GET', 'POST'], '/pings/view/{slug}', ViewPingController::class);
$routeCollector->addRoute(['GET', 'POST'], '/pings/edit/{slug}', EditPingController::class);
$routeCollector->addRoute(['GET', 'POST'], '/pings/checkin/{pingId}', PingCheckinController::class);

// Reminders
$routeCollector->addRoute(['GET', 'POST'], '/reminders[/{archives:archives}]', RemindersIndexController::class);
$routeCollector->addRoute(['GET', 'POST'], '/reminders/create', CreateReminderController::class);
$routeCollector->addRoute(['GET', 'POST'], '/reminders/view/{slug}', ViewReminderController::class);
$routeCollector->addRoute(['GET', 'POST'], '/reminders/edit/{slug}', EditReminderController::class);

// Servers
$routeCollector->addRoute(['GET', 'POST'], '/servers[/{archives:archives}]', ServersIndexController::class);

// Admin
$routeCollector->addRoute(['GET', 'POST'], '/admin', AdminController::class);
$routeCollector->addRoute(['GET', 'POST'], '/admin/create-user', CreateUserController::class);
$routeCollector->addRoute(['GET', 'POST'], '/admin/add-notification-email', AddNotificationEmailController::class);

// Password Reset routes
$routeCollector->get('/iforgot', ForgotPasswordController::class);
$routeCollector->get('/iforgot/check-email', ForgotPasswordController::class);
$routeCollector->get('/iforgot/reset/{token}', PasswordResetController::class);
