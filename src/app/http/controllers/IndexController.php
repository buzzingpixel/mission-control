<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\user\UserApi;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;

class IndexController
{
    private $response;
    private $twigEnvironment;
    private $userApi;

    public function __construct(
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        UserApi $userApi
    ) {
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->userApi = $userApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');

        if (! $this->userApi->fetchCurrentUser()) {
            $response->getBody()->write(
                $this->twigEnvironment->render('LogIn.twig')
            );

            return $response;
        }

        $response->getBody()->write(
            $this->twigEnvironment->render('Index.twig', [
                'someVar' => 'someVal',
            ])
        );

        return $response;
    }
}
