<?php
declare(strict_types=1);

use src\app\http\actions\SendPasswordResetEmailAction;

return [
    'sendPasswordResetEmail' => ['class' => SendPasswordResetEmailAction::class],
];
