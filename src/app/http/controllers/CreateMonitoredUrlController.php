<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;

class CreateMonitoredUrlController
{
    private $userApi;
    private $response;
    private $projectsApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->projectsApi = $projectsApi;
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

        if (! $this->userApi->fetchCurrentUser()->userDataItem('admin')) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify('account/Unauthorized.twig')
            );

            return $response;
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Create Monitored URL',
                'breadCrumbs' => [
                    [
                        'href' => '/monitored-urls',
                        'content' => 'Monitored URLs'
                    ],
                    [
                        'content' => 'Create'
                    ]
                ],
                'title' => 'Create New Monitored URL',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'createMonitoredUrl',
                        'inputs' => [
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'url',
                                'label' => 'URL',
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project',
                                'label' => 'Project',
                                'options' => $this->projectsApi->fetchAsSelectArray(),
                            ]
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
