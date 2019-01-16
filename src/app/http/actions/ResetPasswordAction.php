<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class ResetPasswordAction
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
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $requestMethod = strtolower(
            $request->getServerParams()['REQUEST_METHOD'] ?? 'get'
        );

        if ($requestMethod !== 'post') {
            throw new LogicException(
                'Reset Password Action requires post request'
            );
        }

        $this->userApi->resetPasswordByToken(
            (string) ($request->getParsedBody()['token'] ?? ''),
            (string) ($request->getParsedBody()['password'] ?? '')
        );

        $flashDataModel =$this->flashDataApi->makeFlashDataModel([
            'name' => 'ResetPasswordAction'
        ]);

        $flashDataModel->dataItem('success', true);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/');

        $response = $response->withStatus(303);

        return $response;
    }
}
