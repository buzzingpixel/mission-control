<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use Throwable;
use function date_default_timezone_get;

class AdminController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var QueueApiInterface */
    private $queueApi;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;
    /** @var NotificationEmailsApiInterface */
    private $notificationEmailsApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        QueueApiInterface $queueApi,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService,
        NotificationEmailsApiInterface $notificationEmailsApi
    ) {
        $this->userApi               = $userApi;
        $this->response              = $response;
        $this->queueApi              = $queueApi;
        $this->twigEnvironment       = $twigEnvironment;
        $this->requireLoginService   = $requireLoginService;
        $this->notificationEmailsApi = $notificationEmailsApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $requireLogin = $this->requireLoginService->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
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

        $notificationEmailRows = [];

        $queryModel = $this->notificationEmailsApi->makeQueryModel();
        $queryModel->addOrder('email_address', 'asc');

        foreach ($this->notificationEmailsApi->fetchAll($queryModel) as $notificationEmailModel) {
            $styledStatus = $notificationEmailModel->isActive() ? 'Good' : 'Inactive';

            $notificationEmailRows[] = [
                'inputValue' => $notificationEmailModel->guid(),
                'cols' => [
                    'Email Address' => $notificationEmailModel->emailAddress(),
                    'Status' => $notificationEmailModel->isActive() ? 'Active' : 'Inactive',
                ],
                'colorStyledCols' => ['Status' => $styledStatus],
            ];
        }

        $userRows = [];

        $queryModel = $this->userApi->makeQueryModel();
        $queryModel->addOrder('email_address', 'asc');
        $queryModel->addWhere('guid', $user->getGuidAsBytes(), '!=');

        foreach ($this->userApi->fetchAll($queryModel) as $userModel) {
            $userIsAdmin  = $userModel->getExtendedProperty('is_admin') === 1;
            $styledStatus = 'Inactive';

            if ($userIsAdmin) {
                $styledStatus = 'Good';
            }

            $userRows[] = [
                'inputValue' => $userModel->guid(),
                'actionButtonLink' => '/admin/user-permissions/' . $userModel->guid(),
                'cols' => [
                    'Email' => $userModel->emailAddress(),
                    'Timezone' => $userModel->getExtendedProperty('timezone') ?: date_default_timezone_get(),
                    'Admin' => $userIsAdmin ? 'Yes' : 'No',
                ],
                'colorStyledCols' => ['Admin' => $styledStatus],
            ];
        }

        $queueRows = [];

        foreach ($this->queueApi->fetchAllBatches() as $batch) {
            $addedAt = $batch->addedAt();

            if ($addedAt) {
                $addedAt->setTimezone(new DateTimeZone(
                    $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
                ));
            }

            $queueRows[] = [
                'inputValue' => 'null',
                'cols' => [
                    'Title' => $batch->title(),
                    'Percent Complete' => $batch->percentComplete(),
                    'Added At' => $addedAt ? $addedAt->format('n/j/Y g:i:s a') : '',
                ],
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Admin',
                'title' => 'Admin',
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'tableControlButtons' => [[
                            'href' => '/admin/add-notification-email',
                            'content' => 'Add Notification Email',
                        ],
                        ],
                        'formTitle' => 'Notification Emails',
                        'actionParam' => 'notificationEmailsActions',
                        'actions' => [
                            'disable' => 'Disable Selected',
                            'enable' => 'Enable Selected',
                            'delete' => 'Delete Selected',
                        ],
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => [
                                'Email Address',
                                'Status',
                            ],
                            'rows' => $notificationEmailRows,
                        ],
                    ],
                    [
                        'template' => 'forms/TableListForm.twig',
                        'actionColButtonContent' => 'Permissions',
                        'tableControlButtons' => [
                            [
                                'href' => '/admin/create-user',
                                'content' => 'Create User',
                            ],
                        ],
                        'formTitle' => 'Users',
                        'actionParam' => 'adminUserActions',
                        'actions' => [
                            'promote' => 'Promote Selected to Admin',
                            'demote' => 'Demote Selected from Admin',
                            'delete' => 'Delete Selected',
                        ],
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => [
                                'Email',
                                'Timezone',
                                'Admin',
                            ],
                            'rows' => $userRows,
                        ],
                    ],
                    [
                        'template' => 'forms/TableListForm.twig',
                        'tableControlButtons' => [
                            [
                                'href' => '/admin/queue-failures',
                                'content' => 'View Queue Failures',
                            ],
                        ],
                        'formTitle' => 'Batches Waiting in Queue',
                        'actionParam' => 'null',
                        'table' => [
                            'inputsName' => 'null',
                            'headings' => [
                                'Title',
                                'Percent Complete',
                                'Added At',
                            ],
                            'rows' => $queueRows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
