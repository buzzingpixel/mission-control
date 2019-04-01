<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use Psr\Http\Message\ResponseInterface;

class LogOutAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var FlashDataApiInterface */
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi      = $userApi;
        $this->response     = $response;
        $this->flashDataApi = $flashDataApi;
    }

    /**
     * @throws Http404Exception
     */
    public function __invoke() : ResponseInterface
    {
        if (! $this->userApi->fetchCurrentUser()) {
            throw new Http404Exception();
        }

        $this->userApi->logCurrentUserOut();

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'LogOutAction']);

        $flashDataModel->dataItem('success', true);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/');

        $response = $response->withStatus(303);

        return $response;
    }
}
