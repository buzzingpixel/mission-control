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
use src\app\reminders\interfaces\ReminderApiInterface;
use Throwable;
use function date_default_timezone_get;

class ViewReminderController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var ReminderApiInterface */
    private $reminderApi;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ReminderApiInterface $reminderApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->reminderApi         = $reminderApi;
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

        $fetchParams = $this->reminderApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->reminderApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Reminder with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('Unknown Error');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/reminders/edit/' . $model->slug(),
                'content' => 'Edit Reminder',
            ];
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/reminders',
                'content' => 'Reminders',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Reminder is archived';

            $breadCrumbs[] = [
                'href' => '/reminders/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = ['content' => 'Viewing'];

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        $model->startRemindingOn()->setTimezone(new DateTimeZone($userTimeZone));

        $lastReminderSent = $model->lastReminderSent();

        if ($lastReminderSent) {
            $lastReminderSent->setTimezone(new DateTimeZone($userTimeZone));
        }

        $model->addedAt()->setTimezone(new DateTimeZone($userTimeZone));

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
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
                                'key' => 'Title',
                                'value' => $model->title(),
                            ],
                            [
                                'key' => 'Message',
                                'value' => $model->message() ?: '--',
                            ],
                            [
                                'key' => 'Start Reminding On',
                                'value' => $model->startRemindingOn()->format('F j, Y'),
                            ],
                            [
                                'key' => 'Last Reminder Sent',
                                'value' => $lastReminderSent ?
                                    $lastReminderSent->format('F j, Y') :
                                    '--',
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
