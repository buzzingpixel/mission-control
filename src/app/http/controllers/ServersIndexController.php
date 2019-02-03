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

class ServersIndexController
{
    private $userApi;
    private $response;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
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
                'href' => '/servers/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/servers/create',
                'content' => 'Create Server',
            ];
        }

        // $params = $this->pingApi->makeQueryModel();
        // $params->addOrder('title', 'asc');
        // $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        // $userTimeZone = $user->getExtendedProperty('timezone') ?:
        //     date_default_timezone_get();

        // foreach ($this->pingApi->fetchAll($params) as $model) {
        //     $model->lastPingAt()->setTimezone(new DateTimeZone($userTimeZone));
        //
        //     $status = '--';
        //     $styledStatus = 'Inactive';
        //
        //     if ($model->isActive()) {
        //         $status = 'Active';
        //         $styledStatus = 'Good';
        //
        //         if ($model->hasError()) {
        //             $status = 'Missing';
        //             $styledStatus = 'Error';
        //         } elseif ($model->pendingError()) {
        //             $status = 'Overdue';
        //             $styledStatus = 'Caution';
        //         }
        //     }
        //
        //     $rows[] = [
        //         'inputValue' => $model->guid(),
        //         'actionButtonLink' => '/pings/view/' . $model->slug(),
        //         'cols' => [
        //             'Title' => $model->title(),
        //             'Status' => $status,
        //             'Expect Every' => $model->expectEvery() . ' Minutes',
        //             'Warn After' => $model->warnAfter() . ' Minutes',
        //             'Last Ping' => $model->lastPingAt()->format('n/j/Y g:i a'),
        //         ],
        //         'colorStyledCols' => [
        //             'Status' => $styledStatus,
        //         ],
        //     ];
        // }

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
                    'Server Archives' :
                    'Servers',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/servers',
                        'content' => 'Servers'
                    ],
                    [
                        'content' => 'Viewing Archives'
                    ]
                ] : [],
                'title' => $archivesPage ?
                    'Server Archives' :
                    'Servers',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
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
                    ]
                ],
            ])
        );

        return $response;
    }
}
