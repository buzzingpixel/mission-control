<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\projects\interfaces\ProjectsApiInterface;
use Throwable;
use function date_default_timezone_get;

class ProjectsIndexController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->projectsApi         = $projectsApi;
        $this->requireLoginService = $requireLoginService;
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

        $archivesPage = $request->getAttribute('archives') === 'archives';

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('An unknown error occurred');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if (! $archivesPage) {
            $pageControlButtons[] = [
                'href' => '/projects/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/projects/create',
                'content' => 'Create Project',
            ];
        }

        $params = $this->projectsApi->makeQueryModel();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        foreach ($this->projectsApi->fetchAll($params) as $model) {
            $model->addedAt()->setTimezone(new DateTimeZone(
                $user->getExtendedProperty('timezone') ?:
                    date_default_timezone_get()
            ));

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
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => $archivesPage ? 'Project Archives' : 'Projects',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/projects',
                        'content' => 'Projects',
                    ],
                    ['content' => 'Viewing Archives'],
                ] : [],
                'title' => $archivesPage ? 'Archived Projects' : 'Projects',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'actionParam' => 'projectListActions',
                        'actions' => $actions,
                        'actionColButtonContent' => 'View&nbsp;Project',
                        'table' => [
                            'inputsName' => 'projects[]',
                            'headings' => [
                                'Title',
                                'Slug',
                                'Description',
                                'Added',
                            ],
                            'widths' => ['Description' => '30%'],
                            'rows' => $rows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
