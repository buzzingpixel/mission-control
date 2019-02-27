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
use corbomite\http\exceptions\Http404Exception;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;

class EditReminderController
{
    private $userApi;
    private $response;
    private $projectsApi;
    private $reminderApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        ReminderApiInterface $reminderApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->projectsApi = $projectsApi;
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

        if (! $user = $this->userApi->fetchCurrentUser()) {
            throw new LogicException('Unknown Error');
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        if ($user->getExtendedProperty('is_admin') !== 1) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify(
                    'account/Unauthorized.twig'
                )
            );

            return $response;
        }

        $fetchParams = $this->reminderApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->reminderApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Reminder with slug "' . $request->getAttribute('slug') . '" not found'
            );
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

        $breadCrumbs[] = [
            'href' => '/reminders/view/' . $model->slug(),
            'content' => 'View',
        ];

        $breadCrumbs[] = [
            'content' => 'Edit'
        ];

        $model->startRemindingOn()->setTimezone(
            new DateTimeZone(date_default_timezone_get())
        );

        $selectParams = $this->reminderApi->makeQueryModel();
        $selectParams->addOrder('title', 'asc');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => 'Edit Reminder: ' . $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => 'Edit Reminder: ' . $model->title(),
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editReminder',
                        'inputs' => [
                            [
                                'template' => 'Hidden',
                                'type' => 'hidden',
                                'name' => 'guid',
                                'value' => $model->guid(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                                'value' => $model->title(),
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'message',
                                'label' => 'Message',
                                'value' => $model->message(),
                            ],
                            [
                                'template' => 'Flatpicker',
                                'name' => 'start_reminding_on',
                                'label' => 'Start Reminding On',
                                'value' => $model->startRemindingOn()->format('Y-m-d')
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi
                                    ->fetchAsSelectArray($selectParams),
                                'value' => $model->projectGuid(),
                            ],
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
