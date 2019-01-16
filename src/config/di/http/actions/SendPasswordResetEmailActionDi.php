<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    SendPasswordResetEmailAction::class => function () {
        return new SendPasswordResetEmailAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(EmailApi::class)
        );
    },
];
