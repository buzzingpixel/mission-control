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
use src\app\support\traits\UuidToBytesTrait;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function dd;
use function is_array;
use function trim;

class TicketEditAction
{
    use UuidToBytesTrait;

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
                'Edit Ticket Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $guid = trim($this->requestHelper->post('guid', ''));

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

        $assignedToUserGuid = trim($this->requestHelper->post('assigned_to_user_guid', ''));
        $title              = trim($this->requestHelper->post('title', ''));
        $content            = trim($this->requestHelper->post('content', ''));
        $watchers           = $this->requestHelper->post('watchers');
        $watchers           = is_array($watchers) ? $watchers : [];

        $assignedToUser = null;

        if ($assignedToUserGuid) {
            $params = $this->userApi->makeQueryModel();
            $params->addWhere('guid', $this->uuidToBytes($assignedToUserGuid));
            $assignedToUser = $this->userApi->fetchOne($params);
        }

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'assigned_to_user_guid' => $assignedToUserGuid,
                'title' => $title,
                'content' => $content,
                'watchers' => $watchers,
            ],
        ];

        if (! $assignedToUser) {
            $store['inputErrors']['assigned_to_user_guid'][] = 'This field is required';
        }

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $ticket->assignedToUser($assignedToUser);
        $ticket->title($title);
        $ticket->content($content);

        $ticket->watchers([]);

        if ($watchers) {
            $watcherGuids = [];

            foreach ($watchers as $watcher) {
                $watcherGuids[] = $this->uuidToBytes($watcher);
            }

            $params = $this->userApi->makeQueryModel();
            $params->addWhere('guid', $watcherGuids);

            $watchers = $this->userApi->fetchAll($params);

            if ($watchers) {
                $ticket->watchers($watchers);
            }
        }

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
            'Ticket "' . $ticket->title() . '" edited successfully.'
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
