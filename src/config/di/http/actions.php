<?php
declare(strict_types=1);

use src\app\pings\PingApi;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use src\app\servers\ServerApi;
use src\app\projects\ProjectsApi;
use corbomite\http\RequestHelper;
use src\app\pipelines\PipelineApi;
use src\app\reminders\ReminderApi;
use corbomite\flashdata\FlashDataApi;
use Psr\Container\ContainerInterface;
use src\app\http\actions\LogOutAction;
use src\app\http\actions\EditPingAction;
use src\app\http\actions\PingListActions;
use corbomite\requestdatastore\DataStore;
use src\app\http\actions\CreatePingAction;
use src\app\http\actions\AdminUserActions;
use src\app\http\actions\CreateUserAction;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\http\actions\EditServerAction;
use src\app\http\actions\EditSSHKeyAction;
use src\app\http\actions\ServerListActions;
use src\app\http\actions\SSHKeyListActions;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\http\actions\EditProjectAction;
use src\app\http\actions\EditPipelineAction;
use src\app\http\actions\CreateServerAction;
use src\app\http\actions\CreateSSHKeyAction;
use src\app\http\actions\EditReminderAction;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\PipelineListActions;
use src\app\http\actions\ReminderListActions;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\MonitoredUrlActions;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\UpdateAccountAction;
use src\app\http\actions\ChangePasswordAction;
use src\app\http\actions\CreatePipelineAction;
use src\app\http\actions\CreateReminderAction;
use src\app\http\actions\EditMonitoredUrlAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\AddEmailNotificationAction;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\http\actions\NotificationEmailListActions;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    AddEmailNotificationAction::class => static function (ContainerInterface $di) {
        return new AddEmailNotificationAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class),
            $di->get(NotificationEmailsApi::class)
        );
    },
    AdminUserActions::class => static function (ContainerInterface $di) {
        return new AdminUserActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    ChangePasswordAction::class => static function (ContainerInterface $di) {
        return new ChangePasswordAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreateMonitoredUrlAction::class => static function (ContainerInterface $di) {
        return new CreateMonitoredUrlAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    CreatePingAction::class => static function (ContainerInterface $di) {
        return new CreatePingAction(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreatePipelineAction::class => static function (ContainerInterface $di) {
        return new CreatePipelineAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ServerApi::class),
            $di->get(PipelineApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreateProjectAction::class => static function (ContainerInterface $di) {
        return new CreateProjectAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ProjectsApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreateReminderAction::class => static function (ContainerInterface $di) {
        return new CreateReminderAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ReminderApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreateServerAction::class => static function (ContainerInterface $di) {
        return new CreateServerAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ServerApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreateSSHKeyAction::class => static function (ContainerInterface $di) {
        return new CreateSSHKeyAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ServerApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    CreateUserAction::class => static function (ContainerInterface $di) {
        return new CreateUserAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    EditMonitoredUrlAction::class => static function (ContainerInterface $di) {
        return new EditMonitoredUrlAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    EditPingAction::class => static function (ContainerInterface $di) {
        return new EditPingAction(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    EditPipelineAction::class => static function (ContainerInterface $di) {
        return new EditPipelineAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ServerApi::class),
            $di->get(PipelineApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    EditProjectAction::class => static function (ContainerInterface $di) {
        return new EditProjectAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ProjectsApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    EditReminderAction::class => static function (ContainerInterface $di) {
        return new EditReminderAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ReminderApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    EditServerAction::class => static function (ContainerInterface $di) {
        return new EditServerAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ServerApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    EditSSHKeyAction::class => static function (ContainerInterface $di) {
        return new EditSSHKeyAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(ServerApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    LogOutAction::class => static function (ContainerInterface $di) {
        return new LogOutAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(FlashDataApi::class)
        );
    },
    MonitoredUrlActions::class => static function (ContainerInterface $di) {
        return new MonitoredUrlActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    NotificationEmailListActions::class => static function (ContainerInterface $di) {
        return new NotificationEmailListActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class),
            $di->get(NotificationEmailsApi::class)
        );
    },
    PingListActions::class => static function (ContainerInterface $di) {
        return new PingListActions(
            $di->get(UserApi::class),
            $di->get(PingApi::class),
            new Response(),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    PipelineListActions::class => static function (ContainerInterface $di) {
        return new PipelineListActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(PipelineApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    ProjectListActions::class => static function (ContainerInterface $di) {
        return new ProjectListActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ProjectsApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    ReminderListActions::class => static function (ContainerInterface $di) {
        return new ReminderListActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ReminderApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    ResetPasswordAction::class => static function (ContainerInterface $di) {
        return new ResetPasswordAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(FlashDataApi::class)
        );
    },
    SendPasswordResetEmailAction::class => static function (ContainerInterface $di) {
        return new SendPasswordResetEmailAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(EmailApi::class)
        );
    },
    ServerListActions::class => static function (ContainerInterface $di) {
        return new ServerListActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    SSHKeyListActions::class => static function (ContainerInterface $di) {
        return new SSHKeyListActions(
            $di->get(UserApi::class),
            new Response(),
            $di->get(ServerApi::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
    UpdateAccountAction::class => static function (ContainerInterface $di) {
        return new UpdateAccountAction(
            $di->get(UserApi::class),
            new Response(),
            $di->get(DataStore::class),
            $di->get(FlashDataApi::class),
            $di->get(RequestHelper::class)
        );
    },
];
