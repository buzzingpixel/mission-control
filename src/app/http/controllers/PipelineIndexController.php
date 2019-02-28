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
use src\app\pipelines\interfaces\PipelineApiInterface;

class PipelineIndexController
{
    private $userApi;
    private $response;
    private $pipelineApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        PipelineApiInterface $pipelineApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
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

        $archivesPage = $request->getAttribute('archives') === 'archives';

        if (! $user = $this->userApi->fetchCurrentUser()) {
            throw new LogicException('An unknown error occurred');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if (! $archivesPage) {
            $pageControlButtons[] = [
                'href' => '/pipelines/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/pipelines/create',
                'content' => 'Create Pipeline',
            ];
        }

        $params = $this->pipelineApi->makeQueryModel();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        foreach ($this->pipelineApi->fetchAll($params) as $model) {
            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/pipelines/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'Description' => $model->description(),
                ],
            ];
        }

        $actions = [];

        if ($isAdmin) {
            if ($archivesPage) {
                $actions['unArchive'] = 'Un-Archive Selected';
            }

            if (! $archivesPage) {
                $actions['archive'] = 'Archive Selected';
            }

            $actions['delete'] = 'Delete Selected';
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => $archivesPage ?
                    'Pipeline Archives' :
                    'Pipelines',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/pipelines',
                        'content' => 'Pipelines'
                    ],
                    [
                        'content' => 'Viewing Archives'
                    ]
                ] : [],
                'title' => $archivesPage ?
                    'Pipeline Archives' :
                    'Pipelines',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'actionParam' => 'pipelineListActions',
                        'actions' => $actions,
                        'actionColButtonContent' => 'View&nbsp;Pipeline&nbsp;Details',
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => [
                                'Title',
                                'Description',
                            ],
                            'rows' => $rows,
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
