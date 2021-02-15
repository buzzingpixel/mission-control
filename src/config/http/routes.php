<?php

declare(strict_types=1);

/**
 * @see https://github.com/nikic/FastRoute
 */

use FastRoute\RouteCollector;
use src\app\http\controllers\AccountController;
use src\app\http\controllers\AddNotificationEmailController;
use src\app\http\controllers\AdminController;
use src\app\http\controllers\AdminQueueFailures;
use src\app\http\controllers\AdminQueueFailureView;
use src\app\http\controllers\AdminUserPermissionsController;
use src\app\http\controllers\ChangePasswordController;
use src\app\http\controllers\CreateMonitoredUrlController;
use src\app\http\controllers\CreatePingController;
use src\app\http\controllers\CreatePipelineController;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\CreateReminderController;
use src\app\http\controllers\CreateServerController;
use src\app\http\controllers\CreateSSHKeyController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\EditMonitoredUrlController;
use src\app\http\controllers\EditPingController;
use src\app\http\controllers\EditPipelineController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\EditReminderController;
use src\app\http\controllers\EditServerController;
use src\app\http\controllers\EditSSHKeyController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\PingCheckinController;
use src\app\http\controllers\PingIndexController;
use src\app\http\controllers\PipelineIndexController;
use src\app\http\controllers\PipelineWebhookTriggerGetController;
use src\app\http\controllers\PipelineWebhookTriggerPostController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\RemindersIndexController;
use src\app\http\controllers\RunPipelineController;
use src\app\http\controllers\ServerManageAuthorizedKeys;
use src\app\http\controllers\ServersIndexController;
use src\app\http\controllers\SSHKeyIndexController;
use src\app\http\controllers\TestController;
use src\app\http\controllers\TicketCommentEditController;
use src\app\http\controllers\TicketCreateController;
use src\app\http\controllers\TicketEditController;
use src\app\http\controllers\TicketIndexController;
use src\app\http\controllers\TicketViewController;
use src\app\http\controllers\TicketWorkflowController;
use src\app\http\controllers\ViewMonitoredUrlController;
use src\app\http\controllers\ViewPingController;
use src\app\http\controllers\ViewPipelineController;
use src\app\http\controllers\ViewPipelineJobDetailsController;
use src\app\http\controllers\ViewPipelineJobDetailsControllerBadge;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\ViewReminderController;
use src\app\http\controllers\ViewServerController;
use src\app\http\controllers\ViewSSHKeyController;

/** @var RouteCollector $r */
/** @var RouteCollector $routeCollector */

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
$r->addRoute(['GET', 'POST'], '/servers/authorized-keys/{slug}', ServerManageAuthorizedKeys::class);

// SSH Keys
$r->addRoute(['GET', 'POST'], '/ssh-keys[/{archives:archives}]', SSHKeyIndexController::class);
$r->addRoute(['GET', 'POST'], '/ssh-keys/create', CreateSSHKeyController::class);
$r->addRoute(['GET', 'POST'], '/ssh-keys/view/{slug}', ViewSSHKeyController::class);
$r->addRoute(['GET', 'POST'], '/ssh-keys/edit/{slug}', EditSSHKeyController::class);

// Pipelines
$r->addRoute(['GET', 'POST'], '/pipelines[/{archives:archives}]', PipelineIndexController::class);
$r->addRoute(['GET', 'POST'], '/pipelines/view/{slug}', ViewPipelineController::class);
$r->addRoute(['GET', 'POST'], '/pipelines/view/{slug}/job-details/{guid}', ViewPipelineJobDetailsController::class);
$r->addRoute(['GET', 'POST'], '/pipelines/view/{slug}/job-details/{guid}/badge', ViewPipelineJobDetailsControllerBadge::class);
$r->addRoute(['GET', 'POST'], '/pipelines/create', CreatePipelineController::class);
$r->addRoute(['GET', 'POST'], '/pipelines/edit/{slug}', EditPipelineController::class);
$r->get('/pipelines/run/{slug}', RunPipelineController::class);
$r->get('/pipelines/webhook/trigger/{guid}', PipelineWebhookTriggerGetController::class);
$r->post('/pipelines/webhook/trigger/{guid}', PipelineWebhookTriggerPostController::class);

// Admin
$r->addRoute(['GET', 'POST'], '/admin', AdminController::class);
$r->addRoute(['GET', 'POST'], '/admin/create-user', CreateUserController::class);
$r->addRoute(['GET', 'POST'], '/admin/add-notification-email', AddNotificationEmailController::class);
$r->get('/admin/queue-failures', AdminQueueFailures::class);
$r->get('/admin/queue-failures/{guid}', AdminQueueFailureView::class);
$r->get('/admin/user-permissions/{guid}', AdminUserPermissionsController::class);

// Password Reset routes
$r->get('/iforgot', ForgotPasswordController::class);
$r->get('/iforgot/check-email', ForgotPasswordController::class);
$r->get('/iforgot/reset/{token}', PasswordResetController::class);

// Ticket routes
$r->get('/tickets[/page/{page:\d+}]', TicketIndexController::class);
$r->addRoute(['GET', 'POST'], '/tickets/create', TicketCreateController::class);
$r->get('/tickets/ticket/{guid}', TicketViewController::class);
$r->get('/tickets/ticket/{guid}/edit', TicketEditController::class);
$r->get('/tickets/ticket/{guid}/workflow/{status}', TicketWorkflowController::class);
$r->get('/tickets/edit-comment/{commentGuid}', TicketCommentEditController::class);
