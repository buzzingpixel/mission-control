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
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use Throwable;
use function date_default_timezone_get;

class MonitoredUrlIndexController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;
    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
        $this->monitoredUrlsApi    = $monitoredUrlsApi;
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
                'href' => '/monitored-urls/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/monitored-urls/create',
                'content' => 'Create Monitored URL',
            ];
        }

        $params = $this->monitoredUrlsApi->makeQueryModel();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        foreach ($this->monitoredUrlsApi->fetchAll($params) as $model) {
            $model->checkedAt()->setTimezone(new DateTimeZone($userTimeZone));

            $model->addedAt()->setTimezone(new DateTimeZone($userTimeZone));

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
                    'Monitored URL Archives' :
                    'Monitored URLs',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/monitored-urls',
                        'content' => 'Monitored URLs',
                    ],
                    ['content' => 'Viewing Archives'],
                ] : [],
                'title' => $archivesPage ?
                    'Monitored URL Archives' :
                    'Monitored URLs',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
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
                ],
            ])
        );

        return $response;
    }
}
