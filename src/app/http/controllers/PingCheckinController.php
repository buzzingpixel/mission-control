<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use DateTime;
use Throwable;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\pings\interfaces\PingApiInterface;
use corbomite\http\exceptions\Http404Exception;

class PingCheckinController
{
    private $pingApi;
    private $response;

    public function __construct(
        PingApiInterface $pingApi,
        ResponseInterface $response
    ) {
        $this->pingApi = $pingApi;
        $this->response = $response;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addWhere('ping_id', $request->getAttribute('pingId'));
        $model = $this->pingApi->fetchOne($queryModel);

        if (! $model) {
            throw new Http404Exception('Ping not found');
        }

        $model->pendingError(false);
        $model->hasError(false);
        $model->lastPingAt(new DateTime('now', new DateTimeZone('UTC')));

        $this->pingApi->save($model);

        $response = $this->response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode([
            'status' => 'OK',
        ]));

        return $response;
    }
}
