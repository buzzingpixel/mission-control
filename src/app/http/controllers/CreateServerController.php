<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\servers\interfaces\ServerApiInterface;
use Throwable;

class CreateServerController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->serverApi           = $serverApi;
        $this->twigEnvironment     = $twigEnvironment;
        $this->projectsApi         = $projectsApi;
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
                'metaTitle' => 'Create New Server',
                'breadCrumbs' => [
                    [
                        'href' => '/servers',
                        'content' => 'Servers',
                    ],
                    ['content' => 'Create'],
                ],
                'title' => 'Create New Server',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'createServer',
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
                                'name' => 'address',
                                'label' => 'Address',
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'number',
                                'name' => 'ssh_port',
                                'label' => 'SSH Port',
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'ssh_user_name',
                                'label' => 'SSH User Name',
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'ssh_key_guid',
                                'label' => 'SSH Key',
                                'options' => $this->serverApi
                                    ->fetchSSHKeysAsSelectArray(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi
                                    ->fetchAsSelectArray(),
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
