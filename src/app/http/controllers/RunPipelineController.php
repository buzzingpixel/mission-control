<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\requestdatastore\DataStoreInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class RunPipelineController
{
    private $userApi;
    private $response;
    private $dataStore;
    private $pipelineApi;
    private $flashDataApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        PipelineApiInterface $pipelineApi,
        FlashDataApiInterface $flashDataApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->pipelineApi = $pipelineApi;
        $this->flashDataApi = $flashDataApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
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

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

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
