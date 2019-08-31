<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;

class AdminUserPermissionsController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;
    /** @var PipelineApiInterface */
    private $pipelineApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService,
        PipelineApiInterface $pipelineApi
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
        $this->pipelineApi         = $pipelineApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
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

        $userToEdit = $this->userApi->fetchUser($request->getAttribute('guid'));

        if (! $userToEdit) {
            throw new Http404Exception();
        }

        $title = $userToEdit->emailAddress() . ' Permissions';

        $rows = [];

        foreach ($this->pipelineApi->fetchAll() as $pipeline) {
            $permissions = $userToEdit->userDataItem('permissions');
            $run         = $permissions['pipelines'][$pipeline->guid()]['run'] ?? false;
            $edit        = $permissions['pipelines'][$pipeline->guid()]['edit'] ?? false;

            $rows[] = [
                'cols' => [
                    'Pipeline' => $pipeline->title(),
                    'Run' => [
                        'type' => 'checkbox',
                        'name' => 'permissions[pipelines][' . $pipeline->guid() . '][run]',
                        'checked' => $run,
                    ],
                    'Edit' => [
                        'type' => 'checkbox',
                        'name' => 'permissions[pipelines][' . $pipeline->guid() . '][edit]',
                        'checked' => $edit,
                    ],
                ],
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => $title,
                'title' => $title,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'includeSelectCol' => false,
                        'forceIncludeSubmitButton' => true,
                        'actionParam' => 'editUserPermissions',
                        'hiddenInput' => [
                            'name' => 'user_guid',
                            'value' => $userToEdit->guid(),
                        ],
                        'table' => [
                            'headings' => [
                                'Pipeline',
                                'Run',
                                'Edit',
                            ],
                            'rows' => $rows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
