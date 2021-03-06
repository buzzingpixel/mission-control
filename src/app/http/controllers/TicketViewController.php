<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Parsedown;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function array_values;
use function date_default_timezone_get;

class TicketViewController
{
    /** @var RequireLoginService */
    private $requireLogin;
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var TicketApiContract */
    private $ticketApi;
    /** @var Parsedown */
    private $parsedown;

    public function __construct(
        RequireLoginService $requireLogin,
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        TicketApiContract $ticketApi,
        Parsedown $parsedown
    ) {
        $this->requireLogin    = $requireLogin;
        $this->userApi         = $userApi;
        $this->response        = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->ticketApi       = $ticketApi;
        $this->parsedown       = $parsedown;
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

        $userTimeZoneStr = $user->getExtendedProperty('timezone') ?: date_default_timezone_get();
        $userTimeZone    = new DateTimeZone($userTimeZoneStr);

        $status       = $ticket->status();
        $styledStatus = 'Inactive';

        if ($status === 'resolved') {
            $styledStatus = 'Good';
        } elseif ($status === 'on_hold') {
            $styledStatus = 'Error';
        } elseif ($status === 'in_progress') {
            $styledStatus = 'Caution';
        }

        $keyValueItems = [
            [
                'key' => 'Created By',
                'value' => $ticket->createdByUser()->emailAddress(),
            ],
            [
                'key' => 'Created on',
                'value' => $ticket->addedAt()->setTimezone($userTimeZone)->format('F, j, Y g:ia'),
            ],
        ];

        if ($ticket->resolvedAt()) {
            $keyValueItems[] = [
                'key' => 'Modified on',
                'value' => $ticket->resolvedAt()->setTimezone($userTimeZone)->format('F, j, Y g:ia'),
            ];
        }

        if ($ticket->assignedToUser()) {
            $keyValueItems[] = [
                'key' => 'Assigned To',
                'value' => $ticket->assignedToUser()->emailAddress(),
            ];
        }

        if ($ticket->watchers()) {
            $watchers = '';

            foreach ($ticket->watchers() as $watcher) {
                $watchers .= $watchers ? ', ' : '';
                $watchers .= $watcher->emailAddress();
            }

            $keyValueItems[] = [
                'key' => 'Watchers',
                'value' => $watchers,
            ];
        }

        $keyValueItems[] = [
            'key' => 'Content',
            'value' => $this->parsedown->text($ticket->content()),
        ];

        $pageControlButtons = [];

        $hasTicketControl = false;

        if ($user->getExtendedProperty('is_admin') === 1) {
            $hasTicketControl = true;
        } elseif ($ticket->createdByUser()->guid() === $user->guid()) {
            $hasTicketControl = true;
        } elseif ($ticket->assignedToUser()->guid() === $user->guid()) {
            $hasTicketControl = true;
        } elseif ($ticket->watchers()) {
            foreach ($ticket->watchers() as $watcher) {
                if ($watcher->guid() === $user->guid()) {
                    $hasTicketControl = true;
                    break;
                }
            }
        }

        if ($hasTicketControl) {
            $pageControlButtons = [
                'new' => [
                    'href' => '/tickets/ticket/' . $ticket->guid() . '/workflow/new',
                    'content' => 'Set Status "New"',
                ],
                'in_progress' => [
                    'href' => '/tickets/ticket/' . $ticket->guid() . '/workflow/in_progress',
                    'content' => 'Set Status "In Progress"',
                ],
                'on_hold' => [
                    'href' => '/tickets/ticket/' . $ticket->guid() . '/workflow/on_hold',
                    'content' => 'Set Status "On Hold"',
                ],
                'resolved' => [
                    'href' => '/tickets/ticket/' . $ticket->guid() . '/workflow/resolved',
                    'content' => 'Set Status "Resolved"',
                ],
                'edit' => [
                    'href' => '/tickets/ticket/' . $ticket->guid() . '/edit',
                    'content' => 'Edit Ticket',
                ],
            ];
        }

        unset($pageControlButtons[$ticket->status()]);

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'tags' => [[
                    'content' => $ticket->humanStatus(),
                    'style' => $styledStatus,
                ],
                ],
                'metaTitle' => $ticket->title(),
                'breadCrumbs' => [
                    [
                        'href' => '/tickets',
                        'content' => 'Tickets',
                    ],
                    ['content' => 'Viewing'],
                ],
                'title' => $ticket->title(),
                'pageControlButtons' => array_values($pageControlButtons),
                'includes' => [
                    [
                        'template' => 'includes/KeyValue.twig',
                        'keyValueItems' => $keyValueItems,
                    ],
                    [
                        'template' => 'includes/TicketComments.twig',
                        'threadItems' => $ticket->threadItems(),
                        'displayTimeZoneString' => $user->getExtendedProperty('timezone') ?: date_default_timezone_get(),
                        'currentUserGuid' => $user->guid(),
                        'ticketGuid' => $ticket->guid(),
                        'hasTicketControl' => $hasTicketControl,
                        'userIsAdmin' => $user->getExtendedProperty('is_admin') === 1,
                    ],
                ],
            ])
        );

        return $response;
    }
}
