<?php
declare(strict_types=1);

use src\app\http\actions\LogOutAction;
use src\app\http\actions\AdminUserActions;
use src\app\http\actions\CreatePingAction;
use src\app\http\actions\CreateUserAction;
use src\app\http\actions\EditProjectAction;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\MonitoredUrlActions;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\UpdateAccountAction;
use src\app\http\actions\ChangePasswordAction;
use src\app\http\actions\EditMonitoredUrlAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    'logout' => ['class' => LogOutAction::class],
    'createPing' => ['class' => CreatePingAction::class],
    'createUser' => ['class' => CreateUserAction::class],
    'editProject' => ['class' => EditProjectAction::class],
    'adminUserActions' => ['class' => AdminUserActions::class],
    'createProject' => ['class' => CreateProjectAction::class],
    'resetPassword' => ['class' => ResetPasswordAction::class],
    'updateAccount' => ['class' => UpdateAccountAction::class],
    'changePassword' => ['class' => ChangePasswordAction::class],
    'projectListActions' => ['class' => ProjectListActions::class],
    'editMonitoredUrl' => ['class' => EditMonitoredUrlAction::class],
    'createMonitoredUrl' => ['class' => CreateMonitoredUrlAction::class],
    'monitoredUrlListActions' => ['class' => MonitoredUrlActions::class],
    'sendPasswordResetEmail' => ['class' => SendPasswordResetEmailAction::class],
];
