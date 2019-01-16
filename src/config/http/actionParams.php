<?php
declare(strict_types=1);

use src\app\http\actions\LogOutAction;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    'logout' => ['class' => LogOutAction::class],
    'resetPassword' => ['class' => ResetPasswordAction::class],
    'sendPasswordResetEmail' => ['class' => SendPasswordResetEmailAction::class],
];
