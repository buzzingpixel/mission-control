<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

class CreateProjectAction
{
    private $userApi;
    private $response;
    private $dataStore;
    private $projectsApi;
    private $flashDataApi;
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        ProjectsApiInterface $projectsApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->projectsApi = $projectsApi;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws Http404Exception
     */
    public function __invoke(): ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Create Project Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $title = trim($this->requestHelper->post('title'));
        $description = trim($this->requestHelper->post('description'));

        $store = [
            'inputErrors' => [],
            'inputValues' => compact('title', 'description'),
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $model = $this->projectsApi->createModel();

        $model->title($title);
        $model->description($description);

        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->projectsApi->save($model);
        } catch (ProjectNameNotUniqueException $e) {
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
            'Project "' . $model->title() . '" created successfully'
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
