<?php

declare(strict_types=1);

use src\app\http\actions\AddEmailNotificationAction;
use src\app\http\actions\AdminUserActions;
use src\app\http\actions\ChangePasswordAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\CreatePingAction;
use src\app\http\actions\CreatePipelineAction;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\CreateReminderAction;
use src\app\http\actions\CreateServerAction;
use src\app\http\actions\CreateSSHKeyAction;
use src\app\http\actions\CreateUserAction;
use src\app\http\actions\EditMonitoredUrlAction;
use src\app\http\actions\EditPingAction;
use src\app\http\actions\EditPipelineAction;
use src\app\http\actions\EditProjectAction;
use src\app\http\actions\EditReminderAction;
use src\app\http\actions\EditServerAction;
use src\app\http\actions\EditSSHKeyAction;
use src\app\http\actions\LogOutAction;
use src\app\http\actions\MonitoredUrlActions;
use src\app\http\actions\NotificationEmailListActions;
use src\app\http\actions\PingListActions;
use src\app\http\actions\PipelineListActions;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\ReminderListActions;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\SendPasswordResetEmailAction;
use src\app\http\actions\ServerListActions;
use src\app\http\actions\SSHKeyListActions;
use src\app\http\actions\UpdateAccountAction;
use function DI\autowire;

return [
    AddEmailNotificationAction::class => autowire(),
    AdminUserActions::class => autowire(),
    ChangePasswordAction::class => autowire(),
    CreateMonitoredUrlAction::class => autowire(),
    CreatePingAction::class => autowire(),
    CreatePipelineAction::class => autowire(),
    CreateProjectAction::class => autowire(),
    CreateReminderAction::class => autowire(),
    CreateServerAction::class => autowire(),
    CreateSSHKeyAction::class => autowire(),
    CreateUserAction::class => autowire(),
    EditMonitoredUrlAction::class => autowire(),
    EditPingAction::class => autowire(),
    EditPipelineAction::class => autowire(),
    EditProjectAction::class => autowire(),
    EditReminderAction::class => autowire(),
    EditServerAction::class => autowire(),
    EditSSHKeyAction::class => autowire(),
    LogOutAction::class => autowire(),
    MonitoredUrlActions::class => autowire(),
    NotificationEmailListActions::class => autowire(),
    PingListActions::class => autowire(),
    PipelineListActions::class => autowire(),
    ProjectListActions::class => autowire(),
    ReminderListActions::class => autowire(),
    ResetPasswordAction::class => autowire(),
    SendPasswordResetEmailAction::class => autowire(),
    ServerListActions::class => autowire(),
    SSHKeyListActions::class => autowire(),
    UpdateAccountAction::class => autowire(),
];
