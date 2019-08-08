<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\tickets\interfaces\TicketApiContract;
use src\app\users\AdditionalUserActionsService;
use Throwable;

class TicketEditController
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
    /** @var TicketApiContract */
    private $ticketApi;

    public function __construct(
        RequireLoginService $requireLogin,
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        AdditionalUserActionsService $additionalUserActions,
        TicketApiContract $ticketApi
    ) {
        $this->requireLogin          = $requireLogin;
        $this->userApi               = $userApi;
        $this->response              = $response;
        $this->twigEnvironment       = $twigEnvironment;
        $this->additionalUserActions = $additionalUserActions;
        $this->ticketApi             = $ticketApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
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

        $guid = $request->getAttribute('guid');

        if (! $guid) {
            throw new Http404Exception();
        }

        try {
            $guidBytes = $this->ticketApi->uuidToBytes($guid);
        } catch (Throwable $e) {
            throw new Http404Exception();
        }

        $params = $this->ticketApi->makeQueryModel();
        $params->addWhere('guid', $guidBytes);
        $ticket = $this->ticketApi->fetchOne($params);

        if (! $ticket) {
            throw new Http404Exception();
        }

        $userSelectArray = $this->additionalUserActions->fetchAsSelectArray();

        $watchersValue = [];

        foreach ($ticket->watchers() as $watcher) {
            $watchersValue[] = $watcher->guid();
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Edit Ticket',
                'breadCrumbs' => [
                    [
                        'href' => '/tickets',
                        'content' => 'Tickets',
                    ],
                    [
                        'href' => '/tickets/ticket/' . $ticket->guid(),
                        'content' => $ticket->title(),
                    ],
                    ['content' => 'Edit'],
                ],
                'title' => 'Edit Ticket',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editTicket',
                        'inputs' => [
                            [
                                'template' => 'Hidden',
                                'name' => 'guid',
                                'value' => $ticket->guid(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'assigned_to_user_guid',
                                'label' => 'Assigned To',
                                'options' => $userSelectArray,
                                'value' => $ticket->assignedToUser()->guid(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                                'value' => $ticket->title(),
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'content',
                                'label' => 'Content',
                                'value' => $ticket->content(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'watchers[]',
                                'label' => 'Additional Ticket Watchers (assignee and reporter always receive notifications)',
                                'options' => $userSelectArray,
                                'multiple' => true,
                                'value' => $watchersValue,
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
