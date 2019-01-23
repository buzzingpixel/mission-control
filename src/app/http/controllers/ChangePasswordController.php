<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;

class ChangePasswordController
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
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Change Password',
                'title' => 'Change Password',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'changePassword',
                        'inputs' => [
                            [
                                'template' => 'Text',
                                'type' => 'password',
                                'name' => 'current_password',
                                'label' => 'Current Password',
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'password',
                                'name' => 'new_password',
                                'label' => 'New Password',
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'password',
                                'name' => 'confirm_password',
                                'label' => 'Confirm New Password',
                            ],
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
