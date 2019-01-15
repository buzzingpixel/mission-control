<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use src\app\http\services\RequireLoginService;

return [
    RequireLoginService::class => function () {
        return new RequireLoginService(
            Di::get(TwigEnvironment::class),
            Di::get(UserApi::class),
            new Response()
        );
    },
];
