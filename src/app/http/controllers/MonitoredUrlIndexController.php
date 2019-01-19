<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use DateTimeZone;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class MonitoredUrlIndexController
{
    private $userApi;
    private $response;
    private $twigEnvironment;
    private $monitoredUrlsApi;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
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

        $params = $this->monitoredUrlsApi->createFetchDataParams();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        foreach ($this->monitoredUrlsApi->fetchAll($params) as $model) {
            $model->checkedAt()->setTimezone(new DateTimeZone(
                date_default_timezone_get()
            ));
            $model->addedAt()->setTimezone(new DateTimeZone(
                date_default_timezone_get()
            ));
            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/monitored-urls/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                    'URL' => $model->url(),
                    'Status' => $model->hasError() ? 'Down' :
                        $model->pendingError() ? 'Pending Down' :
                            'Up',
                    'Checked At' => $model->checkedAt()->format('n/j/Y g:i a'),
                ],
                'colLinks' => [
                    'URL' => $model->url(),
                ],
                'colorStyledCols' => [
                    'Status' => $model->hasError() ? 'Error' :
                        $model->pendingError() ? 'Caution' :
                            'Good',
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
                    'Monitored URL Archives' :
                    'Monitored URLs',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/monitored-urls',
                        'content' => 'Monitored URLs'
                    ],
                    [
                        'content' => 'Viewing Archives'
                    ]
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
                                'Checked At'
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
