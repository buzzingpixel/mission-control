<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;

class IndexController
{
    private $response;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twigEnvironment->render('Index.twig')
        );

        return $response;
    }
}
