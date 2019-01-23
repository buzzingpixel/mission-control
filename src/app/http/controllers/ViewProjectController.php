<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use DateTimeZone;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\pings\interfaces\PingApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class ViewProjectController
{
    private $userApi;
    private $pingApi;
    private $response;
    private $projectsApi;
    private $twigEnvironment;
    private $monitoredUrlsApi;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        PingApiInterface $pingApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->userApi = $userApi;
        $this->pingApi = $pingApi;
        $this->response = $response;
        $this->projectsApi = $projectsApi;
        $this->twigEnvironment = $twigEnvironment;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
        $this->requireLoginService = $requireLoginService;
    }

    /** @var bool */
    private $isAdmin;

    /** @var string */
    private $userTimeZone;

    /** @var ProjectModelInterface */
    private $projectModel;

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

        $fetchParams = $this->projectsApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $this->projectModel = $model = $this->projectsApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Project with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $this->isAdmin = $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $this->userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/projects/edit/' . $model->slug(),
                'content' => 'Edit Project',
            ];
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/projects',
                'content' => 'Projects',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This project is archived';

            $breadCrumbs[] = [
                'href' => '/projects/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'content' => 'Viewing Project',
        ];

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => $model->title(),
                'subTitle' => $model->description(),
                'pageControlButtons' => $pageControlButtons,
                'controlsHasBorderBottom' => true,
                'includes' => array_merge(
                    $this->getMonitoredUrls(),
                    $this->getPings()
                ),
            ])
        );

        return $response;
    }

    private function getMonitoredUrls(): array
    {
        $params = $this->monitoredUrlsApi->makeQueryModel();
        $params->addWhere('project_guid', $this->projectsApi->uuidToBytes(
            $this->projectModel->guid()
        ));
        $params->addOrder('title', 'asc');

        if (! $monitoredUrlModels = $this->monitoredUrlsApi->fetchAll($params)) {
            return [];
        }

        $rows = [];

        foreach ($monitoredUrlModels as $model) {
            $model->checkedAt()->setTimezone(new DateTimeZone(
                $this->userTimeZone
            ));

            $model->addedAt()->setTimezone(new DateTimeZone(
                $this->userTimeZone
            ));

            $status = '--';
            $styledStatus = 'Inactive';

            if ($model->isActive()) {
                $status = 'Up';
                $styledStatus = 'Good';

                if ($model->hasError()) {
                    $status = 'Down';
                    $styledStatus = 'Error';
                } elseif ($model->pendingError()) {
                    $status = 'Pending Down';
                    $styledStatus = 'Caution';
                }
            }

            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/monitored-urls/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'URL' => $model->url(),
                    'Status' => $status,
                    'Checked At' => $model->checkedAt()->format('n/j/Y g:i a'),
                ],
                'colLinks' => [
                    'URL' => $model->url(),
                ],
                'colorStyledCols' => [
                    'Status' => $styledStatus,
                ],
            ];
        }

        $actions = [];

        if ($this->isAdmin) {
            $actions['unArchive'] = 'Un-Archive Selected';
            $actions['archive'] = 'Archive Selected';
            $actions['delete'] = 'Delete Selected';
        }

        return [
            [
                'template' => 'forms/TableListForm.twig',
                'formTitle' => 'Monitored URLs',
                'actionParam' => 'monitoredUrlListActions',
                'actions' => $actions,
                'actionColButtonContent' => 'View&nbsp;URL&nbsp;Details',
                'table' => [
                    'inputsName' => 'guids[]',
                    'headings' => [
                        'Title',
                        'URL',
                        'Status',
                        'Checked At'
                    ],
                    'rows' => $rows,
                ],
            ]
        ];
    }

    private function getPings(): array
    {
        $params = $this->monitoredUrlsApi->makeQueryModel();
        $params->addWhere('project_guid', $this->projectsApi->uuidToBytes(
            $this->projectModel->guid()
        ));
        $params->addOrder('title', 'asc');

        if (! $models = $this->pingApi->fetchAll($params)) {
            return [];
        }

        $rows = [];

        foreach ($models as $model) {
            $model->lastPingAt()->setTimezone(new DateTimeZone(
                $this->userTimeZone
            ));

            $status = '--';
            $styledStatus = 'Inactive';

            if ($model->isActive()) {
                $status = 'Active';
                $styledStatus = 'Good';

                if ($model->hasError()) {
                    $status = 'Missing';
                    $styledStatus = 'Error';
                } elseif ($model->pendingError()) {
                    $status = 'Overdue';
                    $styledStatus = 'Caution';
                }
            }

            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/pings/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'Status' => $status,
                    'Expect Every' => $model->expectEvery() . ' Minutes',
                    'Warn After' => $model->warnAfter() . ' Minutes',
                    'Last Ping' => $model->lastPingAt()->format('n/j/Y g:i a'),
                ],
                'colorStyledCols' => [
                    'Status' => $styledStatus,
                ],
            ];
        }

        $actions = [];

        if ($this->isAdmin) {
            $actions['unArchive'] = 'Un-Archive Selected';
            $actions['archive'] = 'Archive Selected';
            $actions['delete'] = 'Delete Selected';
        }

        return [
            [
                'template' => 'forms/TableListForm.twig',
                'formTitle' => 'Pings',
                'actionParam' => 'pingListActions',
                'actions' => $actions,
                'actionColButtonContent' => 'View&nbsp;Ping&nbsp;Details',
                'table' => [
                    'inputsName' => 'guids[]',
                    'headings' => [
                        'Title',
                        'Status',
                        'Expect Every',
                        'Warn After',
                        'Last Ping',
                    ],
                    'rows' => $rows,
                ],
            ]
        ];
    }
}
