<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\http\exceptions\Http404Exception;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;

class EditPipelineController
{
    private $userApi;
    private $response;
    private $serverApi;
    private $projectsApi;
    private $pipelineApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        PipelineApiInterface $pipelineApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->serverApi = $serverApi;
        $this->projectsApi = $projectsApi;
        $this->pipelineApi = $pipelineApi;
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

        $slug = $request->getAttribute('slug');

        $params = $this->pipelineApi->makeQueryModel();
        $params->addWhere('slug', $slug);
        $model = $this->pipelineApi->fetchOne($params);

        if (! $model) {
            throw new Http404Exception('Pipeline with slug "' . $slug . '" not found');
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/pipelines',
                'content' => 'Pipelines',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Pipeline is archived';

            $breadCrumbs[] = [
                'href' => '/pipelines/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'href' => '/pipelines/view/' . $model->slug(),
            'content' => 'View',
        ];

        $breadCrumbs[] = [
            'content' => 'Edit'
        ];

        $pipelineItems = [];

        foreach ($model->pipelineItems() as $item) {
            $serverGuids = [];

            foreach ($item->servers() as $server) {
                $serverGuids = $server->guid();
            }

            $pipelineItems[] = [
                'uuid' => $item->guid(),
                'description' => $item->description(),
                'script' => $item->script(),
                'serverGuids' => $serverGuids,
            ];
        }

        $selectParams = $this->pipelineApi->makeQueryModel();
        $selectParams->addOrder('title', 'asc');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => 'Edit Pipeline: ' . $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => 'Edit Pipeline: ' . $model->title(),
                'formWrap' => true,
                'formActionParam' => 'editPipeline',
                'pageControlButtons' => [[
                    'type' => 'submitInput',
                    'content' => 'Save Pipeline'
                ]],
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'parentIsForm' => true,
                        'submitText' => 'Save Pipeline',
                        'isFullWidth' => true,
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
                                'template' => 'TextArea',
                                'name' => 'description',
                                'label' => 'Description',
                                'value' => $model->description(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi->fetchAsSelectArray($selectParams),
                                'value' => $model->projectGuid(),
                            ],
                            [
                                'template' => 'PipelineBuilder',
                                'name' => 'pipeline_items',
                                'label' => 'Pipeline Items',
                                'serverArray' => $this->serverApi->fetchAsSelectArray($selectParams),
                                'pipelineItems' => $pipelineItems,
                            ],
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
