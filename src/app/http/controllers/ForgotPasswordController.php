<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;

class ForgotPasswordController
{
    private $userApi;
    private $response;
    private $twigEnvironment;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment
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
        if ($this->userApi->fetchCurrentUser()) {
            throw new Http404Exception();
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('ForgotPassword.twig')
        );

        return $response;
    }
}
