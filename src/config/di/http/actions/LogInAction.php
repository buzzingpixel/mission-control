<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use src\app\http\actions\LogInAction;

return [
    LogInAction::class => function () {
        return new LogInAction(Di::get(UserApi::class), new Response());
    },
];
