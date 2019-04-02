<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\servers\interfaces\ServerApiInterface;
use Throwable;
use function array_merge;
use function date_default_timezone_get;

class ViewProjectController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var PingApiInterface */
    private $pingApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var ReminderApiInterface */
    private $reminderApi;
    /** @var RequireLoginService */
    private $requireLoginService;
    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;

    public function __construct(
        UserApiInterface $userApi,
        PingApiInterface $pingApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        ReminderApiInterface $reminderApi,
        RequireLoginService $requireLoginService,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->userApi             = $userApi;
        $this->pingApi             = $pingApi;
        $this->response            = $response;
        $this->serverApi           = $serverApi;
        $this->twigEnvironment     = $twigEnvironment;
        $this->projectsApi         = $projectsApi;
        $this->reminderApi         = $reminderApi;
        $this->requireLoginService = $requireLoginService;
        $this->monitoredUrlsApi    = $monitoredUrlsApi;
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

        $breadCrumbs[] = ['content' => 'Viewing Project'];

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
                    $this->getPings(),
                    $this->getReminders(),
                    $this->getServers()
                ),
            ])
        );

        return $response;
    }

    private function getMonitoredUrls() : array
    {
        $params = $this->monitoredUrlsApi->makeQueryModel();
        $params->addWhere('project_guid', $this->projectsApi->uuidToBytes(
            $this->projectModel->guid()
        ));
        $params->addOrder('title', 'asc');

        $monitoredUrlModels = $this->monitoredUrlsApi->fetchAll($params);

        if (! $monitoredUrlModels) {
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

            $status       = '--';
            $styledStatus = 'Inactive';

            if ($model->isActive()) {
                $status       = 'Up';
                $styledStatus = 'Good';

                if ($model->hasError()) {
                    $status       = 'Down';
                    $styledStatus = 'Error';
                } elseif ($model->pendingError()) {
                    $status       = 'Pending Down';
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
                'colorStyledCols' => ['Status' => $styledStatus],
            ];
        }

        $actions = [];

        if ($this->isAdmin) {
            $actions['unArchive'] = 'Un-Archive Selected';
            $actions['archive']   = 'Archive Selected';
            $actions['delete']    = 'Delete Selected';
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
                        'Checked At',
                    ],
                    'rows' => $rows,
                ],
            ],
        ];
    }

    private function getPings() : array
    {
        $params = $this->monitoredUrlsApi->makeQueryModel();
        $params->addWhere('project_guid', $this->projectsApi->uuidToBytes(
            $this->projectModel->guid()
        ));
        $params->addOrder('title', 'asc');

        $models = $this->pingApi->fetchAll($params);

        if (! $models) {
            return [];
        }

        $rows = [];

        foreach ($models as $model) {
            $model->lastPingAt()->setTimezone(new DateTimeZone(
                $this->userTimeZone
            ));

            $status       = '--';
            $styledStatus = 'Inactive';

            if ($model->isActive()) {
                $status       = 'Active';
                $styledStatus = 'Good';

                if ($model->hasError()) {
                    $status       = 'Missing';
                    $styledStatus = 'Error';
                } elseif ($model->pendingError()) {
                    $status       = 'Overdue';
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
                'colorStyledCols' => ['Status' => $styledStatus],
            ];
        }

        $actions = [];

        if ($this->isAdmin) {
            $actions['unArchive'] = 'Un-Archive Selected';
            $actions['archive']   = 'Archive Selected';
            $actions['delete']    = 'Delete Selected';
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
            ],
        ];
    }

    private function getReminders() : array
    {
        $params = $this->reminderApi->makeQueryModel();
        $params->addWhere('project_guid', $this->projectsApi->uuidToBytes(
            $this->projectModel->guid()
        ));
        $params->addOrder('title', 'asc');

        $models = $this->reminderApi->fetchAll($params);

        if (! $models) {
            return [];
        }

        $rows = [];

        foreach ($models as $model) {
            $startRemindingOn = $model->startRemindingOn();

            if ($startRemindingOn) {
                $startRemindingOn->setTimezone(new DateTimeZone(
                    $this->userTimeZone
                ));
            }

            $lastReminderSent = $model->lastReminderSent();

            if ($lastReminderSent) {
                $lastReminderSent->setTimezone(new DateTimeZone(
                    $this->userTimeZone
                ));
            }

            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/reminders/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'Start Reminding On' => $startRemindingOn ?
                        $startRemindingOn->format('n/j/Y g:i a') :
                        '',
                    'Last Reminder Sent At' => $lastReminderSent ?
                        $lastReminderSent->format('n/j/Y g:i a') :
                        '',
                ],
            ];
        }

        $actions = [];

        if ($this->isAdmin) {
            $actions['unArchive'] = 'Un-Archive Selected';
            $actions['archive']   = 'Archive Selected';
            $actions['delete']    = 'Delete Selected';
        }

        return [
            [
                'template' => 'forms/TableListForm.twig',
                'formTitle' => 'Reminders',
                'actionParam' => 'reminderListActions',
                'actions' => $actions,
                'actionColButtonContent' => 'View&nbsp;Reminder&nbsp;Details',
                'table' => [
                    'inputsName' => 'guids[]',
                    'headings' => [
                        'Title',
                        'Start Reminding On',
                        'Last Reminder Sent At',
                    ],
                    'rows' => $rows,
                ],
            ],
        ];
    }

    private function getServers() : array
    {
        $fetchParams = $this->serverApi->makeQueryModel();
        $fetchParams->addWhere('project_guid', $this->projectsApi->uuidToBytes(
            $this->projectModel->guid()
        ));
        $fetchParams->addOrder('title', 'asc');

        $servers = $this->serverApi->fetchAll($fetchParams);

        if (! $servers) {
            return [];
        }

        $rows = [];

        foreach ($servers as $model) {
            $key    = $model->sshKeyModel();
            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/servers/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'Address' => $model->address(),
                    'SSH Port' => $model->sshPort(),
                    'SSH User Name' => $model->sshUserName(),
                    'SSH Key' => '<a href="/ssh-keys/view/' . $key->slug() . '">' . $key->title() . '</a>',
                ],
            ];
        }

        $actions = [];

        if ($this->isAdmin) {
            $actions['unArchive'] = 'Un-Archive Selected';
            $actions['archive']   = 'Archive Selected';
            $actions['delete']    = 'Delete Selected';
        }

        return [
            [
                'template' => 'forms/TableListForm.twig',
                'formTitle' => 'Servers',
                'actionParam' => 'serverListActions',
                'actions' => $actions,
                'actionColButtonContent' => 'View&nbsp;Server&nbsp;Details',
                'table' => [
                    'inputsName' => 'guids[]',
                    'headings' => [
                        'Title',
                        'Address',
                        'SSH Port',
                        'SSH Key',
                        'SSH User Name',
                    ],
                    'rows' => $rows,
                ],
            ],
        ];
    }
}
