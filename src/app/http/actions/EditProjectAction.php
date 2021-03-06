<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\projects\exceptions\ProjectNameNotUniqueException;
use src\app\projects\interfaces\ProjectsApiInterface;
use function array_values;
use function is_array;
use function trim;

class EditProjectAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var DataStoreInterface */
    private $dataStore;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        ProjectsApiInterface $projectsApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->dataStore     = $dataStore;
        $this->projectsApi   = $projectsApi;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws Http404Exception
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Edit Project Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $fetchParams = $this->projectsApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->projectsApi->uuidToBytes(
            $this->requestHelper->post('guid')
        ));
        $model = $this->projectsApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception();
        }

        $title       = trim($this->requestHelper->post('title'));
        $description = trim($this->requestHelper->post('description'));
        $keyValues   = $this->requestHelper->post('keyValues');

        if (! is_array($keyValues)) {
            $keyValues = [];
        }

        $keyValues = array_values($keyValues);

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'description' => $description,
                'keyValues' => $keyValues,
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
        $model->clearKeyValueItems();

        foreach ($keyValues as $item) {
            if (! isset($item['key']) || ! isset($item['value'])) {
                continue;
            }

            $model->setKeyValueItem($item['key'], $item['value']);
        }

        try {
            $this->projectsApi->save($model);
        } catch (ProjectNameNotUniqueException $e) {
            $store['inputErrors']['title'][] = 'Title must be unique';
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Project "' . $model->title() . '" saved successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/projects/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
