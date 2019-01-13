<?php
declare(strict_types=1);

namespace src\app\http\actions;

use Throwable;
use LogicException;
use corbomite\user\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogInAction
{
    private $userApi;
    private $response;

    public function __construct(UserApi $userApi, ResponseInterface $response)
    {
        $this->userApi = $userApi;
        $this->response = $response;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $requestMethod = strtolower(
            $request->getServerParams()['REQUEST_METHOD'] ?? 'get'
        );

        if ($requestMethod !== 'post') {
            throw new LogicException('Log In Action requires post request');
        }

        $email = $request->getParsedBody()['email'] ?? '';
        $password = $request->getParsedBody()['password'] ?? '';

        try {
            $this->userApi->logUserIn($email, $password);
            $response = $this->response->withHeader(
                'Location',
                $request->getUri()->getPath()
            );
            $response = $response->withStatus(303);
            return $response;
        } catch (Throwable $e) {
            // TODO: deal with errors
            var_dump($e->getMessage());
            die;
        }
    }
}
