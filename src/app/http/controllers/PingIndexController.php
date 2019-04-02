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
use src\app\pings\interfaces\PingApiInterface;
use Throwable;
use function date_default_timezone_get;

class PingIndexController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var PingApiInterface */
    private $pingApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        PingApiInterface $pingApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->pingApi             = $pingApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
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
                'href' => '/pings/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/pings/create',
                'content' => 'Create Ping',
            ];
        }

        $params = $this->pingApi->makeQueryModel();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        foreach ($this->pingApi->fetchAll($params) as $model) {
            $model->lastPingAt()->setTimezone(new DateTimeZone($userTimeZone));

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
                    'Ping Archives' :
                    'Pings',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/pings',
                        'content' => 'Pings',
                    ],
                    ['content' => 'Viewing Archives'],
                ] : [],
                'title' => $archivesPage ?
                    'Ping Archives' :
                    'Pings',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
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
                ],
            ])
        );

        return $response;
    }
}
