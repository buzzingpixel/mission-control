<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\pings\PingApi;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\queue\QueueApi;
use src\app\servers\ServerApi;
use src\app\projects\ProjectsApi;
use src\app\reminders\ReminderApi;
use corbomite\twig\TwigEnvironment;
use src\app\utilities\TimeZoneListUtility;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\http\controllers\AdminController;
use src\app\http\services\RequireLoginService;
use src\app\http\controllers\AccountController;
use src\app\http\controllers\EditPingController;
use src\app\http\controllers\ViewPingController;
use src\app\http\controllers\PingIndexController;
use src\app\http\controllers\CreatePingController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\SSHKeyIndexController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\PingCheckinController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\CreateServerController;
use src\app\http\controllers\CreateSSHKeyController;
use src\app\http\controllers\EditReminderController;
use src\app\http\controllers\ServersIndexController;
use src\app\http\controllers\ViewReminderController;
use src\app\http\controllers\CreateProjectController;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\ChangePasswordController;
use src\app\http\controllers\CreateReminderController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\RemindersIndexController;
use src\app\http\controllers\RenderErrorPageController;
use src\app\http\controllers\EditMonitoredUrlController;
use src\app\http\controllers\ViewMonitoredUrlController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\CreateMonitoredUrlController;
use src\app\http\controllers\AddNotificationEmailController;

return [
    AccountController::class => function () {
        return new AccountController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(TimeZoneListUtility::class)
        );
    },
    AddNotificationEmailController::class => function () {
        return new AddNotificationEmailController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    AdminController::class => function () {
        return new AdminController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(QueueApi::class),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(NotificationEmailsApi::class)
        );
    },
    ChangePasswordController::class => function () {
        return new ChangePasswordController(
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateMonitoredUrlController::class => function () {
        return new CreateMonitoredUrlController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreatePingController::class => function () {
        return new CreatePingController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateProjectController::class => function () {
        return new CreateProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateReminderController::class => function () {
        return new CreateReminderController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateServerController::class => function () {
        return new CreateServerController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(ServerApi::class),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateSSHKeyController::class => function () {
        return new CreateSSHKeyController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateUserController::class => function () {
        return new CreateUserController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    EditMonitoredUrlController::class => function () {
        return new EditMonitoredUrlController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    EditPingController::class => function () {
        return new EditPingController(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    EditProjectController::class => function () {
        return new EditProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    EditReminderController::class => function () {
        return new EditReminderController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(ReminderApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    ForgotPasswordController::class => function () {
        return new ForgotPasswordController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class)
        );
    },
    MonitoredUrlIndexController::class => function () {
        return new MonitoredUrlIndexController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    PasswordResetController::class => function () {
        return new PasswordResetController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class)
        );
    },
    PingCheckinController::class => function () {
        return new PingCheckinController(
            Di::get(PingApi::class),
            new Response()
        );
    },
    PingIndexController::class => function () {
        return new PingIndexController(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    ProjectsIndexController::class => function () {
        return new ProjectsIndexController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    RemindersIndexController::class => function () {
        return new RemindersIndexController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ReminderApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    RenderErrorPageController::class => function () {
        return new RenderErrorPageController(
            Di::get(TwigEnvironment::class),
            new Response()
        );
    },
    ServersIndexController::class => function () {
        return new ServersIndexController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    SSHKeyIndexController::class => function () {
        return new SSHKeyIndexController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    ViewMonitoredUrlController::class => function () {
        return new ViewMonitoredUrlController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    ViewPingController::class => function () {
        return new ViewPingController(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    ViewProjectController::class => function () {
        return new ViewProjectController(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(ReminderApi::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    ViewReminderController::class => function () {
        return new ViewReminderController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ReminderApi::class),
            Di::get(RequireLoginService::class)
        );
    },
];
