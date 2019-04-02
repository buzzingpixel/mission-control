<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use Throwable;

class CreateSSHKeyController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $requireLogin = $this->requireLoginService->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('Unknown Error');
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        if ($user->getExtendedProperty('is_admin') !== 1) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify(
                    'account/Unauthorized.twig'
                )
            );

            return $response;
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Create SSH Key',
                'breadCrumbs' => [
                    [
                        'href' => '/ssh-keys',
                        'content' => 'SSH Keys',
                    ],
                    ['content' => 'Create SSH Key'],
                ],
                'title' => 'Create SSH Key',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'createSshKey',
                        'inputs' => [
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                            ],
                            [
                                'template' => 'Checkbox',
                                'name' => 'generate',
                                'label' => 'Generate?',
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'public',
                                'label' => 'Public Key (ignored if Generate selected)',
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'private',
                                'label' => 'Private Key (ignored if Generate selected)',
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
