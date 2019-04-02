<?php

declare(strict_types=1);

use corbomite\twig\TwigEnvironment;
use corbomite\user\UserApi;
use Psr\Container\ContainerInterface;
use src\app\http\services\RenderPipelineInnerComponents;
use src\app\http\services\RequireLoginService;
use src\app\pipelines\PipelineApi;
use Zend\Diactoros\Response;

return [
    RequireLoginService::class => static function (ContainerInterface $di) {
        return new RequireLoginService(
            $di->get(TwigEnvironment::class),
            $di->get(UserApi::class),
            new Response()
        );
    },
    RenderPipelineInnerComponents::class => static function (ContainerInterface $di) {
         return new RenderPipelineInnerComponents(
             $di->get(UserApi::class),
             $di->get(TwigEnvironment::class),
             $di->get(PipelineApi::class)
         );
    },
];
