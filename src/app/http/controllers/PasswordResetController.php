<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class PasswordResetController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment
    ) {
        $this->userApi         = $userApi;
        $this->response        = $response;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $token = $request->getAttribute('token');

        if ($this->userApi->fetchCurrentUser()) {
            throw new Http404Exception();
        }

        $user = $this->userApi->getUserByPasswordResetToken($token);

        if (! $user) {
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
