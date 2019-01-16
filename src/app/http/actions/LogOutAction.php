<?php
declare(strict_types=1);

namespace src\app\http\actions;

use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class LogOutAction
{
    private $userApi;
    private $response;
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->flashDataApi = $flashDataApi;
    }

    /**
     * @throws Http404Exception
     */
    public function __invoke(): ResponseInterface
    {
        if (! $this->userApi->fetchCurrentUser()) {
            throw new Http404Exception();
        }

        $this->userApi->logCurrentUserOut();

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'LogOutAction'
        ]);

        $flashDataModel->dataItem('success', true);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/');

        $response = $response->withStatus(303);

        return $response;
    }
}
