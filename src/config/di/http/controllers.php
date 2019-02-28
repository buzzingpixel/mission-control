<?php
declare(strict_types=1);

use src\app\pings\PingApi;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\queue\QueueApi;
use src\app\servers\ServerApi;
use src\app\projects\ProjectsApi;
use src\app\reminders\ReminderApi;
use src\app\pipelines\PipelineApi;
use corbomite\twig\TwigEnvironment;
use Psr\Container\ContainerInterface;
use src\app\utilities\TimeZoneListUtility;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\http\controllers\AdminController;
use src\app\http\services\RequireLoginService;
use src\app\http\controllers\AccountController;
use src\app\http\controllers\EditPingController;
use src\app\http\controllers\ViewPingController;
use src\app\http\controllers\PingIndexController;
use src\app\http\controllers\EditServerController;
use src\app\http\controllers\EditSSHKeyController;
use src\app\http\controllers\CreatePingController;
use src\app\http\controllers\ViewServerController;
use src\app\http\controllers\ViewSSHKeyController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\SSHKeyIndexController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\PingCheckinController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\ViewPipelineController;
use src\app\http\controllers\CreateServerController;
use src\app\http\controllers\CreateSSHKeyController;
use src\app\http\controllers\EditReminderController;
use src\app\http\controllers\ServersIndexController;
use src\app\http\controllers\ViewReminderController;
use src\app\http\controllers\PipelineIndexController;
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
    AccountController::class => static function (ContainerInterface $di) {
        return new AccountController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class),
            $di->get(TimeZoneListUtility::class)
        );
    },
    AddNotificationEmailController::class => static function (ContainerInterface $di) {
        return new AddNotificationEmailController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    AdminController::class => static function (ContainerInterface $di) {
        return new AdminController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(QueueApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class),
            $di->get(NotificationEmailsApi::class)
        );
    },
    ChangePasswordController::class => static function (ContainerInterface $di) {
        return new ChangePasswordController(
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreateMonitoredUrlController::class => static function (ContainerInterface $di) {
        return new CreateMonitoredUrlController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreatePingController::class => static function (ContainerInterface $di) {
        return new CreatePingController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreateProjectController::class => static function (ContainerInterface $di) {
        return new CreateProjectController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreateReminderController::class => static function (ContainerInterface $di) {
        return new CreateReminderController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreateServerController::class => static function (ContainerInterface $di) {
        return new CreateServerController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreateSSHKeyController::class => static function (ContainerInterface $di) {
        return new CreateSSHKeyController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    CreateUserController::class => static function (ContainerInterface $di) {
        return new CreateUserController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    EditMonitoredUrlController::class => static function (ContainerInterface $di) {
        return new EditMonitoredUrlController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    EditPingController::class => static function (ContainerInterface $di) {
        return new EditPingController(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    EditProjectController::class => static function (ContainerInterface $di) {
        return new EditProjectController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    EditReminderController::class => static function (ContainerInterface $di) {
        return new EditReminderController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(ReminderApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    EditServerController::class => static function (ContainerInterface $di) {
        return new EditServerController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    EditSSHKeyController::class => static function (ContainerInterface $di) {
        return new EditSSHKeyController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    ForgotPasswordController::class => static function (ContainerInterface $di) {
        return new ForgotPasswordController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class)
        );
    },
    MonitoredUrlIndexController::class => static function (ContainerInterface $di) {
        return new MonitoredUrlIndexController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    PasswordResetController::class => static function (ContainerInterface $di) {
        return new PasswordResetController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class)
        );
    },
    PingCheckinController::class => static function (ContainerInterface $di) {
        return new PingCheckinController(
            $di->get(PingApi::class),
            new Response()
        );
    },
    PingIndexController::class => static function (ContainerInterface $di) {
        return new PingIndexController(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    PipelineIndexController::class => static function (ContainerInterface $di) {
        return new PipelineIndexController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(PipelineApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    ProjectsIndexController::class => static function (ContainerInterface $di) {
        return new ProjectsIndexController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    RemindersIndexController::class => static function (ContainerInterface $di) {
        return new RemindersIndexController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ReminderApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    RenderErrorPageController::class => static function (ContainerInterface $di) {
        return new RenderErrorPageController(
            $di->get(TwigEnvironment::class),
            new Response()
        );
    },
    ServersIndexController::class => static function (ContainerInterface $di) {
        return new ServersIndexController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    SSHKeyIndexController::class => static function (ContainerInterface $di) {
        return new SSHKeyIndexController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    ViewMonitoredUrlController::class => static function (ContainerInterface $di) {
        return new ViewMonitoredUrlController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    ViewPingController::class => static function (ContainerInterface $di) {
        return new ViewPingController(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    ViewPipelineController::class => static function (ContainerInterface $di) {
        return new ViewPipelineController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(PipelineApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    ViewProjectController::class => static function (ContainerInterface $di) {
        return new ViewProjectController(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(ProjectsApi::class),
            $di->get(ReminderApi::class),
            $di->get(RequireLoginService::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    ViewReminderController::class => static function (ContainerInterface $di) {
        return new ViewReminderController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(TwigEnvironment::class),
            $di->get(ReminderApi::class),
            $di->get(RequireLoginService::class)
        );
    },
    ViewServerController::class => static function (ContainerInterface $di) {
        return new ViewServerController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
    ViewSSHKeyController::class => static function (ContainerInterface $di) {
        return new ViewSSHKeyController(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(TwigEnvironment::class),
            $di->get(RequireLoginService::class)
        );
    },
];
