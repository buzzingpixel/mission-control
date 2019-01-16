<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\flashdata\FlashDataApi;
use src\app\http\actions\ResetPasswordAction;

return [
    ResetPasswordAction::class => function () {
        return new ResetPasswordAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class)
        );
    },
];
