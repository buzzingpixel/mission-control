<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;

class PasswordResetController
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
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $token = $request->getAttribute('token');

        if ($this->userApi->fetchCurrentUser() ||
            ! $user = $this->userApi->getUserByPasswordResetToken($token)
        ) {
            throw new Http404Exception();
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify(
                'account/ResetPassword.twig',
                [
                    'user' => $user,
                    'token' => $token,
                ]
            )
        );

        return $response;
    }
}
