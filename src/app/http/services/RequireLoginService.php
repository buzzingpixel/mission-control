<?php

declare(strict_types=1);

namespace src\app\http\services;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequireLoginService
{
    /** @var TwigEnvironment */
    private $twig;
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;

    public function __construct(
        TwigEnvironment $twig,
        UserApiInterface $userApi,
        ResponseInterface $response
    ) {
        $this->twig     = $twig;
        $this->userApi  = $userApi;
        $this->response = $response;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ?ResponseInterface
    {
        return $this->requireLogin();
    }

    /**
     * @throws Throwable
     */
    public function requireLogin() : ?ResponseInterface
    {
        if ($this->userApi->fetchCurrentUser()) {
            return null;
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twig->renderAndMinify('account/LogIn.twig')
        );

        return $response;
    }
}
