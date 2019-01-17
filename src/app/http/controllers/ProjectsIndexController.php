<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use corbomite\user\interfaces\UserApiInterface;
use src\app\http\services\RequireLoginService;
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
    public function __invoke(): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $tableControlButtons = [];

        if ($this->userApi->fetchCurrentUser()->userDataItem('admin')) {
            $tableControlButtons[] = [
                'href' => '/projects/create',
                'content' => 'Create Project'
            ];
        }

        $params = $this->projectsApi->createFetchDataParams();
        $params->addOrder('title', 'asc');
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

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('forms/TableListForm.twig', [
                'actionParam' => 'projectListActions',
                'title' => 'Projects',
                'tableControlButtons' => $tableControlButtons,
                'actions' => [
                    'archive' => 'Archive Selected',
                    'delete' => 'Delete Selected',
                ],
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
