<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;

class RunPipelineController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var PipelineApiInterface */
    private $pipelineApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        PipelineApiInterface $pipelineApi,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi      = $userApi;
        $this->response     = $response;
        $this->pipelineApi  = $pipelineApi;
        $this->flashDataApi = $flashDataApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $params = $this->pipelineApi->makeQueryModel();
        $params->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->pipelineApi->fetchOne($params);

        if (! $model) {
            throw new Http404Exception();
        }

        $this->pipelineApi->initJobFromPipelineModel($model);

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Job for pipeline "' . $model->title() . '" added to queue.'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/pipelines/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
