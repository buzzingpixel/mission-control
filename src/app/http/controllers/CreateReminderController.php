<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use src\app\projects\interfaces\ProjectsApiInterface;
use Throwable;

class CreateReminderController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->projectsApi         = $projectsApi;
        $this->requireLoginService = $requireLoginService;
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

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Create Reminder',
                'breadCrumbs' => [
                    [
                        'href' => '/reminders',
                        'content' => 'Reminders',
                    ],
                    ['content' => 'Create'],
                ],
                'title' => 'Create Reminder',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'createReminder',
                        'inputs' => [
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'message',
                                'label' => 'Message',
                            ],
                            [
                                'template' => 'Flatpicker',
                                'name' => 'start_reminding_on',
                                'label' => 'Start Reminding On',
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi
                                    ->fetchAsSelectArray(),
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
