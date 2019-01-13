<?php
declare(strict_types=1);

use corbomite\di\Di;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use src\app\http\controllers\IndexController;

return [
    IndexController::class => function () {
        return new IndexController(
            new Response(),
            Di::get(TwigEnvironment::class)
        );
    },
];
