<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class ViewMonitoredUrlController
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

        $fetchParams = $this->monitoredUrlsApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->monitoredUrlsApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Monitored URL with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        if (! $user = $this->userApi->fetchCurrentUser()) {
            throw new LogicException('Unknown Error');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/monitored-urls/edit/' . $model->slug(),
                'content' => 'Edit Monitored URL',
            ];
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/monitored-urls',
                'content' => 'Monitored URLs',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Monitored URL is archived';

            $breadCrumbs[] = [
                'href' => '/monitored-urls/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'content' => 'Viewing',
        ];

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

        $rows = [];

        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->limit(50);
        $queryModel->addWhere('monitored_url_guid', $model->getGuidAsBytes());
        $queryModel->addOrder('event_at');

        foreach ($this->monitoredUrlsApi->fetchIncidents($queryModel) as $incident) {
            $incident->eventAt()->setTimezone(new \DateTimeZone(
                $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
            ));

            $styledType = strtolower($incident->eventType()) === 'down' ?
                'Error' :
                'Good';

            $rows[] = [
                'inputValue' => 'null',
                'cols' => [
                    'Type' => $incident->eventType(),
                    'Status Code' => $incident->statusCode(),
                    'Message' => $incident->message(),
                    'Date' => $incident->eventAt()->format('n/j/Y g:i a'),
                ],
                'colorStyledCols' => [
                    'Type' => $styledType,
                ],
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'tags' => [[
                    'content' => $status,
                    'style' => $styledStatus,
                ]],
                'notification' => $notification,
                'metaTitle' => $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => $model->title(),
                'subTitle' => $model->url(),
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'table' => [
                            'inputsName' => 'null',
                            'headings' => [
                                'Type',
                                'Status Code',
                                'Message',
                                'Date',
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
