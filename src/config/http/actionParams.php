<?php
declare(strict_types=1);

use src\app\http\actions\LogOutAction;
use src\app\http\actions\EditProjectAction;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    'logout' => ['class' => LogOutAction::class],
    'editProject' => ['class' => EditProjectAction::class],
    'createProject' => ['class' => CreateProjectAction::class],
    'resetPassword' => ['class' => ResetPasswordAction::class],
    'projectListActions' => ['class' => ProjectListActions::class],
    'createMonitoredUrl' => ['class' => CreateMonitoredUrlAction::class],
    'sendPasswordResetEmail' => ['class' => SendPasswordResetEmailAction::class],
];
