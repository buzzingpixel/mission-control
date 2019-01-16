<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use src\app\http\controllers\PasswordResetController;

return [
    PasswordResetController::class => function () {
        return new PasswordResetController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class)
        );
    },
];
