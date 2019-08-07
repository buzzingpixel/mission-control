<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function trim;

class TicketAddComment
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var RequestHelperInterface */
    private $requestHelper;
    /** @var DataStoreInterface */
    private $dataStore;
    /** @var TicketApiContract */
    private $ticketApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        RequestHelperInterface $requestHelper,
        DataStoreInterface $dataStore,
        TicketApiContract $ticketApi,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->requestHelper = $requestHelper;
        $this->dataStore     = $dataStore;
        $this->ticketApi     = $ticketApi;
        $this->flashDataApi  = $flashDataApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Add Ticket Comment Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $ticketGuid = trim($this->requestHelper->post('ticketGuid', ''));
        $comment    = trim($this->requestHelper->post('comment', ''));

        if (! $ticketGuid || ! $comment) {
            throw new Http404Exception();
        }

        try {
            $ticketGuidBytes = $this->ticketApi->uuidToBytes($ticketGuid);
        } catch (Throwable $e) {
            throw new Http404Exception();
        }

        $params = $this->ticketApi->makeQueryModel();
        $params->addWhere('guid', $ticketGuidBytes);
        $ticket = $this->ticketApi->fetchOne($params);

        if (! $ticket) {
            throw new Http404Exception();
        }

        $threadItemModel = $this->ticketApi->createThreadItemModel();

        $threadItemModel->ticket($ticket);

        $threadItemModel->user($user);

        $threadItemModel->content($comment);

        $this->ticketApi->saveThreadItem($threadItemModel);

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Comment added!'
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
