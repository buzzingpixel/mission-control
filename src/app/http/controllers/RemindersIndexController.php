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
use corbomite\user\interfaces\UserApiInterface;
use src\app\reminders\interfaces\ReminderApiInterface;

class RemindersIndexController
{
    private $userApi;
    private $response;
    private $reminderApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ReminderApiInterface $reminderApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->reminderApi = $reminderApi;
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
                'href' => '/reminders/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/reminders/create',
                'content' => 'Create Reminder',
            ];
        }

        $params = $this->reminderApi->makeQueryModel();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        foreach ($this->reminderApi->fetchAll($params) as $model) {
            $startRemindingOn = $model->startRemindingOn();

            if ($startRemindingOn) {
                $startRemindingOn->setTimezone(new DateTimeZone($userTimeZone));
            }

            $lastReminderSent = $model->lastReminderSent();

            if ($lastReminderSent) {
                $lastReminderSent->setTimezone(new DateTimeZone($userTimeZone));
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

        $actions = [
            'markComplete' => 'Mark Complete',
        ];

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
                    'Reminder Archives' :
                    'Reminders',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/reminders',
                        'content' => 'Reminders'
                    ],
                    [
                        'content' => 'Viewing Archives'
                    ]
                ] : [],
                'title' => $archivesPage ?
                    'Reminders Archives' :
                    'Reminders',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
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
                    ]
                ],
            ])
        );

        return $response;
    }
}
