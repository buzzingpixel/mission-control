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

return [
    'addNotificationEmail' => AddEmailNotificationAction::class,
    'adminUserActions' => AdminUserActions::class,
    'changePassword' => ChangePasswordAction::class,
    'createMonitoredUrl' => CreateMonitoredUrlAction::class,
    'createPing' => CreatePingAction::class,
    'createPipeline' => CreatePipelineAction::class,
    'createProject' => CreateProjectAction::class,
    'createReminder' => CreateReminderAction::class,
    'createServer' => CreateServerAction::class,
    'createSshKey' => CreateSSHKeyAction::class,
    'createUser' => CreateUserAction::class,
    'editMonitoredUrl' => EditMonitoredUrlAction::class,
    'editPing' => EditPingAction::class,
    'editPipeline' => EditPipelineAction::class,
    'editProject' => EditProjectAction::class,
    'editReminder' => EditReminderAction::class,
    'editServer' => EditServerAction::class,
    'editSshKey' => EditSSHKeyAction::class,
    'logout' => LogOutAction::class,
    'monitoredUrlListActions' => MonitoredUrlActions::class,
    'notificationEmailsActions' => NotificationEmailListActions::class,
    'pingListActions' => PingListActions::class,
    'pipelineListActions' => PipelineListActions::class,
    'projectListActions' => ProjectListActions::class,
    'reminderListActions' => ReminderListActions::class,
    'resetPassword' => ResetPasswordAction::class,
    'sendPasswordResetEmail' => SendPasswordResetEmailAction::class,
    'serverListActions' => ServerListActions::class,
    'sshKeyListActions' => SSHKeyListActions::class,
    'updateAccount' => UpdateAccountAction::class,
];
