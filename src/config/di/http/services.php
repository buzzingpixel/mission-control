<?php
declare(strict_types=1);

use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use Psr\Container\ContainerInterface;
use src\app\http\services\RequireLoginService;

return [
    RequireLoginService::class => function (ContainerInterface $di) {
        return new RequireLoginService(
            $di->get(TwigEnvironment::class),
            $di->get(UserApi::class),
            new Response()
        );
    },
];
