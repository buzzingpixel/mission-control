<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\exceptions\Http500Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class PipelineListActions
{
    private $userApi;
    private $response;
    private $pipelineApi;
    private $flashDataApi;
    private $requestHelper;

    private $guids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        PipelineApiInterface $pipelineApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->pipelineApi = $pipelineApi;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;

        $guids = $this->requestHelper->post('guids');

        if (! $guids) {
            return;
        }

        foreach ($guids as $guid) {
            $this->guids[] = $this->pipelineApi->uuidToBytes($guid);
        }
    }

    /**
     * @throws Http404Exception
     * @throws Http500Exception
     */
    public function __invoke(): ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Pipeline List Actions requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        if (! $this->guids) {
            throw new Http500Exception('No Pings specified');
        }

        $fetchParams = $this->pipelineApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->guids);
        $models = $this->pipelineApi->fetchAll($fetchParams);

        $verb = '';

        foreach ($models as $model) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'archive':
                    $verb = 'archived';
                    $this->pipelineApi->archive($model);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->pipelineApi->delete($model);
                    break;
                case 'unArchive':
                    $verb = 'un-archived';
                    $this->pipelineApi->unArchive($model);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = \count($models) > 1 ?
            'Pipelines' :
            'Pipeline';

        $flashDataModel->dataItem(
            'content',
            $singularPlural . ' ' . $verb . ' successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/pipelines');

        $response = $response->withStatus(303);

        return $response;
    }
}
