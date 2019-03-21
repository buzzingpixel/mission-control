<?php
declare(strict_types=1);

use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use src\app\pipelines\PipelineApi;
use corbomite\twig\TwigEnvironment;
use Psr\Container\ContainerInterface;
use src\app\http\services\RequireLoginService;
use src\app\http\services\RenderPipelineInnerComponents;

return [
    RequireLoginService::class => function (ContainerInterface $di) {
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
             $di->get(PipelineApi::class),
             $di->get(RequireLoginService::class)
         );
    }
];
