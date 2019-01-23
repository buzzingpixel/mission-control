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

class ViewPingController
{
    private $userApi;
    private $pingApi;
    private $response;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        PingApiInterface $pingApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->pingApi = $pingApi;
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

        $fetchParams = $this->pingApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->pingApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Ping with slug "' . $request->getAttribute('slug') . '" not found'
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
                'href' => '/pings/edit/' . $model->slug(),
                'content' => 'Edit Ping',
            ];
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/pings',
                'content' => 'Pings',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Ping is archived';

            $breadCrumbs[] = [
                'href' => '/pings/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'content' => 'Viewing',
        ];

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

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        $model->lastPingAt()->setTimezone(new DateTimeZone($userTimeZone));

        $model->addedAt()->setTimezone(new DateTimeZone($userTimeZone));

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
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'includes/KeyValue.twig',
                        'keyValueItems' => [
                            [
                                'key' => 'Check In Url',
                                'value' => getenv('SITE_URL') . '/pings/checkin/' . $model->pingId(),
                            ],
                            [
                                'key' => 'Expect Every',
                                'value' => $model->expectEvery() . ' Minutes',
                            ],
                            [
                                'key' => 'Warn After',
                                'value' => $model->warnAfter() . ' Minutes',
                            ],
                            [
                                'key' => 'Last Ping',
                                'value' => $model->lastPingAt()->format('n/j/Y g:i a'),
                            ],
                            [
                                'key' => 'Added At',
                                'value' => $model->addedAt()->format('n/j/Y g:i a'),
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
