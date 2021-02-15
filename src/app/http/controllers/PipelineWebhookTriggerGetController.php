<?php

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;

class PipelineWebhookTriggerGetController
{
    /** @var ResponseInterface */
    private $response;
    /** @var PipelineApiInterface */
    private $pipelineApi;

    public function __construct(
        ResponseInterface $response,
        PipelineApiInterface $pipelineApi
    ) {
        $this->response = $response;
        $this->pipelineApi = $pipelineApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        ServerRequestInterface $request
    ) : ResponseInterface {
        $fetchParams = $this->pipelineApi->makeQueryModel();

        $fetchParams->addWhere(
            'guid',
            $this->pipelineApi->uuidToBytes(
                $request->getAttribute('guid')
            )
        );

        $pipeline = $this->pipelineApi->fetchOne($fetchParams);

        if (! $pipeline) {
            throw new Http404Exception(
                'Pipeline with guid "' .
                $request->getAttribute('guid') .
                '" not found'
            );
        }

        $this->pipelineApi->initJobFromPipelineModel($pipeline);

        $response = $this->response->withHeader(
            'Content-Type',
            'application/json'
        );

        $response->getBody()->write(
            json_encode([
                'status' => 'OK',
                'triggered' => 'true',
            ])
        );

        return $response;
    }
}
