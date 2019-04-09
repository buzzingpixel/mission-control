<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\servers\interfaces\ServerApiInterface;
use Throwable;
use function trim;

class AddAuthorizedKeyToServer
{
    /** @var RequestHelperInterface */
    private $requestHelper;
    /** @var UserApiInterface */
    private $userApi;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var ResponseInterface */
    private $response;

    public function __construct(
        RequestHelperInterface $requestHelper,
        UserApiInterface $userApi,
        ServerApiInterface $serverApi,
        FlashDataApiInterface $flashDataApi,
        ResponseInterface $response
    ) {
        $this->requestHelper = $requestHelper;
        $this->userApi       = $userApi;
        $this->serverApi     = $serverApi;
        $this->flashDataApi  = $flashDataApi;
        $this->response      = $response;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Add Authorized Key to Server requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $key        = trim($this->requestHelper->post('key'));
        $serverGuid = trim($this->requestHelper->post('server_guid'));

        if (! $key || ! $serverGuid) {
            throw new LogicException('Required input missing');
        }

        $query = $this->serverApi->makeQueryModel();
        $query->addWhere('guid', $this->serverApi->uuidToBytes($this->requestHelper->post('server_guid')));
        $server = $this->serverApi->fetchOne($query);

        if (! $server) {
            throw new Http404Exception('Specified server could not be found');
        }

        $this->serverApi->addServerAuthorizedKey($key, $server);

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Authorized key added successfully!'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            trim($this->requestHelper->post('return_url')) ?: '/'
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
