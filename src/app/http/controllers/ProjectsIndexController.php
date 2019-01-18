<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;

class ProjectsIndexController
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
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        $archivesPage = $request->getAttribute('archives') === 'archives';

        $isAdmin = $this->userApi->fetchCurrentUser()->userDataItem('admin');

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $tableControlButtons = [];

        if (! $archivesPage) {
            $tableControlButtons[] = [
                'href' => '/projects/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $tableControlButtons[] = [
                'href' => '/projects/create',
                'content' => 'Create Project',
            ];
        }

        $params = $this->projectsApi->createFetchDataParams();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');
        $rows = [];
        foreach ($this->projectsApi->fetchProjects($params) as $model) {
            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/projects/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'Slug' => $model->slug(),
                    'Description' => $model->description(),
                    'Added' => $model->addedAt()->format('n/j/Y'),
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
            $this->twigEnvironment->renderAndMinify('forms/TableListForm.twig', [
                'metaTitle' => $archivesPage ? 'Project Archives' : 'Projects',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/projects',
                        'content' => 'Projects'
                    ],
                    [
                        'content' => 'Viewing Archives'
                    ]
                ] : [],
                'actionParam' => 'projectListActions',
                'title' => $archivesPage ? 'Archived Projects' : 'Projects',
                'tableControlButtons' => $tableControlButtons,
                'actions' => $actions,
                'actionColButtonContent' => 'View Project',
                'table' => [
                    'inputsName' => 'projects[]',
                    'headings' => [
                        'Title',
                        'Slug',
                        'Description',
                        'Added'
                    ],
                    'rows' => $rows,
                ],
            ])
        );

        return $response;
    }
}
