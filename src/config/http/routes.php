<?php
declare(strict_types=1);

/**
 * @see https://github.com/nikic/FastRoute
 */

/** @var \FastRoute\RouteCollector $r */
/** @var \FastRoute\RouteCollector $routeCollector */

use src\app\http\controllers\TestController;
use src\app\http\controllers\AdminController;
use src\app\http\controllers\AccountController;
use src\app\http\controllers\EditPingController;
use src\app\http\controllers\ViewPingController;
use src\app\http\controllers\PingIndexController;
use src\app\http\controllers\EditServerController;
use src\app\http\controllers\CreatePingController;
use src\app\http\controllers\EditSSHKeyController;
use src\app\http\controllers\ViewServerController;
use src\app\http\controllers\ViewSSHKeyController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\SSHKeyIndexController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\PingCheckinController;
use src\app\http\controllers\ServersIndexController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\ViewReminderController;
use src\app\http\controllers\EditReminderController;
use src\app\http\controllers\CreateServerController;
use src\app\http\controllers\CreateSSHKeyController;
use src\app\http\controllers\PipelineIndexController;
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
    $r->addRoute(['GET', 'POST'], '/test', new TestController());
}

// Account
$r->addRoute(['GET', 'POST'], '/account', AccountController::class);
$r->addRoute(['GET', 'POST'], '/account/change-password', ChangePasswordController::class);

// Projects
$r->addRoute(['GET', 'POST'], '/', ProjectsIndexController::class);
$r->addRoute(['GET', 'POST'], '/projects[/{archives:archives}]', ProjectsIndexController::class);
$r->addRoute(['GET', 'POST'], '/projects/create', CreateProjectController::class);
$r->addRoute(['GET', 'POST'], '/projects/view/{slug}', ViewProjectController::class);
$r->addRoute(['GET', 'POST'], '/projects/edit/{slug}', EditProjectController::class);

// Monitored URLS
$r->addRoute(['GET', 'POST'], '/monitored-urls[/{archives:archives}]', MonitoredUrlIndexController::class);
$r->addRoute(['GET', 'POST'], '/monitored-urls/create', CreateMonitoredUrlController::class);
$r->addRoute(['GET', 'POST'], '/monitored-urls/view/{slug}', ViewMonitoredUrlController::class);
$r->addRoute(['GET', 'POST'], '/monitored-urls/edit/{slug}', EditMonitoredUrlController::class);

// Pings
$r->addRoute(['GET', 'POST'], '/pings[/{archives:archives}]', PingIndexController::class);
$r->addRoute(['GET', 'POST'], '/pings/create', CreatePingController::class);
$r->addRoute(['GET', 'POST'], '/pings/view/{slug}', ViewPingController::class);
$r->addRoute(['GET', 'POST'], '/pings/edit/{slug}', EditPingController::class);
$r->addRoute(['GET', 'POST'], '/pings/checkin/{pingId}', PingCheckinController::class);

// Reminders
$r->addRoute(['GET', 'POST'], '/reminders[/{archives:archives}]', RemindersIndexController::class);
$r->addRoute(['GET', 'POST'], '/reminders/create', CreateReminderController::class);
$r->addRoute(['GET', 'POST'], '/reminders/view/{slug}', ViewReminderController::class);
$r->addRoute(['GET', 'POST'], '/reminders/edit/{slug}', EditReminderController::class);

// Servers
$r->addRoute(['GET', 'POST'], '/servers[/{archives:archives}]', ServersIndexController::class);
$r->addRoute(['GET', 'POST'], '/servers/create', CreateServerController::class);
$r->addRoute(['GET', 'POST'], '/servers/view/{slug}', ViewServerController::class);
$r->addRoute(['GET', 'POST'], '/servers/edit/{slug}', EditServerController::class);

// SSH Keys
$r->addRoute(['GET', 'POST'], '/ssh-keys[/{archives:archives}]', SSHKeyIndexController::class);
$r->addRoute(['GET', 'POST'], '/ssh-keys/create', CreateSSHKeyController::class);
$r->addRoute(['GET', 'POST'], '/ssh-keys/view/{slug}', ViewSSHKeyController::class);
$r->addRoute(['GET', 'POST'], '/ssh-keys/edit/{slug}', EditSSHKeyController::class);

// Pipelines
$r->addRoute(['GET', 'POST'], '/pipelines[/{archives:archives}]', PipelineIndexController::class);

// Admin
$r->addRoute(['GET', 'POST'], '/admin', AdminController::class);
$r->addRoute(['GET', 'POST'], '/admin/create-user', CreateUserController::class);
$r->addRoute(['GET', 'POST'], '/admin/add-notification-email', AddNotificationEmailController::class);

// Password Reset routes
$r->get('/iforgot', ForgotPasswordController::class);
$r->get('/iforgot/check-email', ForgotPasswordController::class);
$r->get('/iforgot/reset/{token}', PasswordResetController::class);
