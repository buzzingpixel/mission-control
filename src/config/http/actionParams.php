<?php
declare(strict_types=1);

use src\app\http\actions\LogOutAction;
use src\app\http\actions\EditPingAction;
use src\app\http\actions\PingListActions;
use src\app\http\actions\EditServerAction;
use src\app\http\actions\EditSSHKeyAction;
use src\app\http\actions\AdminUserActions;
use src\app\http\actions\CreatePingAction;
use src\app\http\actions\CreateUserAction;
use src\app\http\actions\ServerListActions;
use src\app\http\actions\EditProjectAction;
use src\app\http\actions\SSHKeyListActions;
use src\app\http\actions\CreateServerAction;
use src\app\http\actions\EditReminderAction;
use src\app\http\actions\CreateSSHKeyAction;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\ReminderListActions;
use src\app\http\actions\MonitoredUrlActions;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\UpdateAccountAction;
use src\app\http\actions\CreatePipelineAction;
use src\app\http\actions\ChangePasswordAction;
use src\app\http\actions\CreateReminderAction;
use src\app\http\actions\EditMonitoredUrlAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\AddEmailNotificationAction;
use src\app\http\actions\NotificationEmailListActions;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    'logout' => ['class' => LogOutAction::class],
    'editPing' => ['class' => EditPingAction::class],
    'createPing' => ['class' => CreatePingAction::class],
    'editServer' => ['class' => EditServerAction::class],
    'editSshKey' => ['class' => EditSSHKeyAction::class],
    'createUser' => ['class' => CreateUserAction::class],
    'editProject' => ['class' => EditProjectAction::class],
    'createServer' => ['class' => CreateServerAction::class],
    'editReminder' => ['class' => EditReminderAction::class],
    'pingListActions' => ['class' => PingListActions::class],
    'createSshKey' => ['class' => CreateSSHKeyAction::class],
    'adminUserActions' => ['class' => AdminUserActions::class],
    'createProject' => ['class' => CreateProjectAction::class],
    'resetPassword' => ['class' => ResetPasswordAction::class],
    'updateAccount' => ['class' => UpdateAccountAction::class],
    'changePassword' => ['class' => ChangePasswordAction::class],
    'createPipeline' => ['class' => CreatePipelineAction::class],
    'serverListActions' => ['class' => ServerListActions::class],
    'sshKeyListActions' => ['class' => SSHKeyListActions::class],
    'createReminder' => ['class' => CreateReminderAction::class],
    'projectListActions' => ['class' => ProjectListActions::class],
    'editMonitoredUrl' => ['class' => EditMonitoredUrlAction::class],
    'reminderListActions' => ['class' => ReminderListActions::class],
    'createMonitoredUrl' => ['class' => CreateMonitoredUrlAction::class],
    'monitoredUrlListActions' => ['class' => MonitoredUrlActions::class],
    'addNotificationEmail' => ['class' => AddEmailNotificationAction::class],
    'sendPasswordResetEmail' => ['class' => SendPasswordResetEmailAction::class],
    'notificationEmailsActions' => ['class' => NotificationEmailListActions::class],
];
