<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;

class EditServerController
{
    private $userApi;
    private $response;
    private $serverApi;
    private $projectsApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->serverApi = $serverApi;
        $this->projectsApi = $projectsApi;
        $this->twigEnvironment = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        if (! $user = $this->userApi->fetchCurrentUser()) {
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

        $fetchParams = $this->serverApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->serverApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Server with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/servers',
                'content' => 'Servers',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Server is archived';

            $breadCrumbs[] = [
                'href' => '/servers/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'href' => '/servers/view/' . $model->slug(),
            'content' => 'View',
        ];

        $breadCrumbs[] = [
            'content' => 'Edit'
        ];

        $selectParams = $this->serverApi->makeQueryModel();
        $selectParams->addOrder('title', 'asc');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => 'Edit Server: ' . $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => 'Edit Server: ' . $model->title(),
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editServer',
                        'inputs' => [
                            [
                                'template' => 'Hidden',
                                'type' => 'hidden',
                                'name' => 'guid',
                                'value' => $model->guid(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                                'value' => $model->title(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'address',
                                'label' => 'Address',
                                'value' => $model->address(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'number',
                                'name' => 'ssh_port',
                                'label' => 'SSH Port',
                                'value' => $model->sshPort(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'ssh_user_name',
                                'label' => 'SSH User Name',
                                'value' => $model->sshUserName(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'ssh_key_guid',
                                'label' => 'SSH Key',
                                'options' => $this->serverApi
                                    ->fetchSSHKeysAsSelectArray($selectParams),
                                'value' => $model->sshKeyModel()->guid(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi
                                    ->fetchAsSelectArray($selectParams),
                                'value' => $model->projectGuid(),
                            ],
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
