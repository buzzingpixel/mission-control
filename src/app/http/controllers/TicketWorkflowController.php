<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function dd;
use function in_array;

class TicketWorkflowController
{
    private const STATUSES = [
        'new',
        'in_progress',
        'on_hold',
        'resolved',
    ];

    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TicketApiContract */
    private $ticketApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TicketApiContract $ticketApi,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi      = $userApi;
        $this->response     = $response;
        $this->ticketApi    = $ticketApi;
        $this->flashDataApi = $flashDataApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $guid   = $request->getAttribute('guid');
        $status = $request->getAttribute('status');

        if (! $guid || ! in_array($status, self::STATUSES)) {
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

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new Http404Exception();
        }

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

        if (! $hasTicketControl) {
            throw new Http404Exception();
        }

        $ticket->status($status);

        try {
            $this->ticketApi->save($ticket);
        } catch (InvalidModel $e) {
            dd($e);

            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Ticket status set successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/tickets/ticket/' . $ticket->guid()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
