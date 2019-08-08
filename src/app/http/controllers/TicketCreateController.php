<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use src\app\users\AdditionalUserActionsService;
use Throwable;

class TicketCreateController
{
    /** @var RequireLoginService */
    private $requireLogin;
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var AdditionalUserActionsService */
    private $additionalUserActions;

    public function __construct(
        RequireLoginService $requireLogin,
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        AdditionalUserActionsService $additionalUserActions
    ) {
        $this->requireLogin          = $requireLogin;
        $this->userApi               = $userApi;
        $this->response              = $response;
        $this->twigEnvironment       = $twigEnvironment;
        $this->additionalUserActions = $additionalUserActions;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $requireLogin = $this->requireLogin->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('An unknown error occurred');
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

        $userSelectArray = $this->additionalUserActions->fetchAsSelectArray();

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Create Ticket',
                'breadCrumbs' => [
                    [
                        'href' => '/tickets',
                        'content' => 'Tickets',
                    ],
                    ['content' => 'Create Ticket'],
                ],
                'title' => 'Create Ticket',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'createTicket',
                        'inputs' => [
                            [
                                'template' => 'Select',
                                'name' => 'assigned_to_user_guid',
                                'label' => 'Assigned To',
                                'options' => $userSelectArray,
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'content',
                                'label' => 'Content (use markdown for formatting)',
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'watchers[]',
                                'label' => 'Additional Ticket Watchers (assignee and reporter always receive notifications)',
                                'options' => $userSelectArray,
                                'multiple' => true,
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
