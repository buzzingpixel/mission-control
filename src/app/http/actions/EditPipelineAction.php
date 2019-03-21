<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\requestdatastore\DataStoreInterface;
use src\app\servers\interfaces\ServerApiInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\servers\exceptions\TitleNotUniqueException;
use corbomite\flashdata\interfaces\FlashDataApiInterface;
use function trim;
use function is_array;

class EditPipelineAction
{
    private $userApi;
    private $response;
    private $dataStore;
    private $serverApi;
    private $pipelineApi;
    private $flashDataApi;
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        ServerApiInterface $serverApi,
        PipelineApiInterface $pipelineApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->serverApi = $serverApi;
        $this->pipelineApi = $pipelineApi;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Edit Pipeline Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $params = $this->pipelineApi->makeQueryModel();
        $params->addWhere('guid', $this->pipelineApi->uuidToBytes($this->requestHelper->post('guid')));
        $model = $this->pipelineApi->fetchOne($params);

        if (! $model) {
            throw new Http404Exception();
        }

        $title = trim($this->requestHelper->post('title'));
        $description = trim($this->requestHelper->post('description'));
        $projectGuid = trim($this->requestHelper->post('project_guid'));
        $items = $this->requestHelper->post('pipeline_items');

        $items = is_array($items) ? $items : [];

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'description' => $description,
                'pipeline_items' => $items,
            ],
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $model->title($title);

        $model->description($description);

        $model->projectGuid($projectGuid);

        $pipelineItemModels = [];

        foreach ($items as $item) {
            if (! isset($item['script']) || ! $item['script']) {
                continue;
            }

            $itemModel = $this->pipelineApi->createPipelineItemModel();

            $uuid = $item['uuid'] ?? null;

            if ($uuid) {
                foreach ($model->pipelineItems() as $existingItemModel) {
                    if ($existingItemModel->guid() !== $uuid) {
                        continue;
                    }

                    $itemModel = $existingItemModel;

                    break;
                }
            }

            $itemModel->description($item['description'] ?? '');

            $itemModel->script($item['script']);

            $servers = $item['servers'] ?? [];

            $servers = is_array($servers) ? $servers : [];

            if (! $servers) {
                $itemModel->servers([]);
                $pipelineItemModels[] = $itemModel;
                continue;
            }

            $servers = array_map(function (string $guid) {
                return $this->serverApi->uuidToBytes($guid);
            }, $servers);

            $queryModel = $this->serverApi->makeQueryModel();

            $queryModel->addWhere('guid', $servers);

            $itemModel->servers($this->serverApi->fetchAll($queryModel));

            $pipelineItemModels[] = $itemModel;
        }

        $model->pipelineItems($pipelineItemModels);

        try {
            $this->pipelineApi->save($model);
        } catch (TitleNotUniqueException $e) {
            $store['inputErrors']['title'][] = 'Title must be unique';
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Pipeline "' . $model->title() . '" saved successfully.'
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
