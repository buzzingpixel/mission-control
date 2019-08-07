<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function trim;

class TicketCommentEditAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var RequestHelperInterface */
    private $requestHelper;
    /** @var TicketApiContract */
    private $ticketApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        RequestHelperInterface $requestHelper,
        TicketApiContract $ticketApi,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->requestHelper = $requestHelper;
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
                'Edit Ticket Comment Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $commentGuid = trim($this->requestHelper->post('guid', ''));
        $content     = trim($this->requestHelper->post('content'));

        if (! $commentGuid) {
            throw new Http404Exception();
        }

        try {
            $commentGuidBytes = $this->ticketApi->uuidToBytes($commentGuid);
        } catch (Throwable $e) {
            throw new Http404Exception();
        }

        $params = $this->ticketApi->makeQueryModel();
        $params->addWhere('guid', $commentGuidBytes);
        $comment = $this->ticketApi->fetchOneThreadItem($params);

        if (! $comment) {
            throw new Http404Exception();
        }

        $comment->content($content);

        $this->ticketApi->saveThreadItem($comment);

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Comment edited successfully.'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/tickets/ticket/' . $comment->ticket()->guid()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
