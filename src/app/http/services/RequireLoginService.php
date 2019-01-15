<?php
declare(strict_types=1);

namespace src\app\http\services;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use corbomite\user\interfaces\UserApiInterface;

class RequireLoginService
{
    private $twig;
    private $userApi;
    private $response;

    public function __construct(
        TwigEnvironment $twig,
        UserApiInterface $userApi,
        ResponseInterface $response
    ) {
        $this->twig = $twig;
        $this->userApi = $userApi;
        $this->response = $response;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ?ResponseInterface
    {
        return $this->requireLogin();
    }

    /**
     * @throws Throwable
     */
    public function requireLogin(): ?ResponseInterface
    {
        if ($this->userApi->fetchCurrentUser()) {
            return null;
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write($this->twig->render('LogIn.twig'));

        return $response;
    }
}
