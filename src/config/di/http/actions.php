<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\pings\PingApi;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use src\app\servers\ServerApi;
use src\app\projects\ProjectsApi;
use corbomite\http\RequestHelper;
use src\app\reminders\ReminderApi;
use corbomite\flashdata\FlashDataApi;
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
use src\app\http\actions\CreateServerAction;
use src\app\http\actions\CreateSSHKeyAction;
use src\app\http\actions\EditReminderAction;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\ReminderListActions;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\MonitoredUrlActions;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\UpdateAccountAction;
use src\app\http\actions\ChangePasswordAction;
use src\app\http\actions\CreateReminderAction;
use src\app\http\actions\EditMonitoredUrlAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\AddEmailNotificationAction;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\http\actions\NotificationEmailListActions;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    AddEmailNotificationAction::class => function () {
        return new AddEmailNotificationAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class),
            Di::get(NotificationEmailsApi::class)
        );
    },
    AdminUserActions::class => function () {
        return new AdminUserActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    ChangePasswordAction::class => function () {
        return new ChangePasswordAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    CreateMonitoredUrlAction::class => function () {
        return new CreateMonitoredUrlAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    CreatePingAction::class => function () {
        return new CreatePingAction(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    CreateProjectAction::class => function () {
        return new CreateProjectAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ProjectsApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    CreateReminderAction::class => function () {
        return new CreateReminderAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ReminderApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    CreateServerAction::class => function () {
        return new CreateServerAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ServerApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    CreateSSHKeyAction::class => function () {
        return new CreateSSHKeyAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ServerApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    CreateUserAction::class => function () {
        return new CreateUserAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    EditMonitoredUrlAction::class => function () {
        return new EditMonitoredUrlAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    EditPingAction::class => function () {
        return new EditPingAction(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    EditProjectAction::class => function () {
        return new EditProjectAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ProjectsApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    EditReminderAction::class => function () {
        return new EditReminderAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ReminderApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    EditServerAction::class => function () {
        return new EditServerAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ServerApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    EditSSHKeyAction::class => function () {
        return new EditSSHKeyAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ServerApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    LogOutAction::class => function () {
        return new LogOutAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class)
        );
    },
    MonitoredUrlActions::class => function () {
        return new MonitoredUrlActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    NotificationEmailListActions::class => function () {
        return new NotificationEmailListActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class),
            Di::get(NotificationEmailsApi::class)
        );
    },
    PingListActions::class => function () {
        return new PingListActions(
            Di::get(UserApi::class),
            Di::get(PingApi::class),
            new Response(),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    ProjectListActions::class => function () {
        return new ProjectListActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(ProjectsApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    ReminderListActions::class => function () {
        return new ReminderListActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(ReminderApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    ResetPasswordAction::class => function () {
        return new ResetPasswordAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class)
        );
    },
    SendPasswordResetEmailAction::class => function () {
        return new SendPasswordResetEmailAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(EmailApi::class)
        );
    },
    ServerListActions::class => function () {
        return new ServerListActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(ServerApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    SSHKeyListActions::class => function () {
        return new SSHKeyListActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(ServerApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    UpdateAccountAction::class => function () {
        return new UpdateAccountAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
];
