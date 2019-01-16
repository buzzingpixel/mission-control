<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\flashdata\FlashDataApi;
use src\app\http\actions\LogOutAction;

return [
    LogOutAction::class => function () {
        return new LogOutAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class)
        );
    },
];
