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

class CreatePipelineController
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
                'metaTitle' => 'Create New Pipeline',
                'breadCrumbs' => [
                    [
                        'href' => '/pipelines',
                        'content' => 'Pipelines',
                    ],
                    ['content' => 'Create'],
                ],
                'title' => 'Create New Pipeline',
                'formWrap' => true,
                'formActionParam' => 'createPipeline',
                'pageControlButtons' => [[
                    'type' => 'submitInput',
                    'content' => 'Save New Pipeline',
                ],
                ],
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'parentIsForm' => true,
                        'submitText' => 'Save New Pipeline',
                        'isFullWidth' => true,
                        'inputs' => [
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'description',
                                'label' => 'Description',
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi
                                    ->fetchAsSelectArray(),
                            ],
                            [
                                'template' => 'TextArea',
                                'codeEditor' => true,
                                'name' => 'run_before_every_item',
                                'label' => 'Run Before Every Item',
                            ],
                            [
                                'template' => 'PipelineBuilder',
                                'name' => 'pipeline_items',
                                'label' => 'Pipeline Items',
                                'serverArray' => $this->serverApi
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
