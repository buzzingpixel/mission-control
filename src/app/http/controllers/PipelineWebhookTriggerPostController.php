<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;

class PipelineWebhookTriggerPostController
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

        $triggered = false;

        if ($pipeline->webhookCheckForBranch() === '') {
            $triggered = true;

            $this->pipelineApi->initJobFromPipelineModel(
                $pipeline
            );
        } else {
            if ($this->checkResponseForBranch(
                $pipeline->webhookCheckForBranch(),
                $request
            )) {
                $triggered = true;

                $this->pipelineApi->initJobFromPipelineModel(
                    $pipeline
                );
            }
        }

        $response = $this->response->withHeader(
            'Content-Type',
            'application/json'
        );

        $response->getBody()->write(
            json_encode([
                'status' => 'OK',
                'triggered' => $triggered ? 'true' : 'false',
            ])
        );

        return $response;
    }

    private function checkResponseForBranch(
        string $webhookCheckForBranch,
        ServerRequestInterface $request
    ) : bool {
        $json = json_decode(
            (string) $request->getBody(),
            true
        );

        $json = is_array($json) ? $json : [];

        $bitBucketBranchName = $json['push']['changes'][0]['new']['name'] ??
            null;

        if ($bitBucketBranchName !== null) {
            return $bitBucketBranchName === $webhookCheckForBranch;
        }

        $githubBranchName = $json['ref'] ?? null;

        if ($githubBranchName) {
            return mb_strpos($githubBranchName, $webhookCheckForBranch) !== false;
        }

        return false;
    }
}
