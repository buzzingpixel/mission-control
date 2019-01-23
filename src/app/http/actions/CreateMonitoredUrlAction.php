<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\monitoredurls\exceptions\MonitoredUrlNameNotUniqueException;

class CreateMonitoredUrlAction
{
    private $userApi;
    private $response;
    private $dataStore;
    private $flashDataApi;
    private $requestHelper;
    private $monitoredUrlsApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    /**
     * @throws \Exception
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
        $url = trim($this->requestHelper->post('url'));
        $project_guid = trim($this->requestHelper->post('project_guid'));

        $store = [
            'inputErrors' => [],
            'inputValues' => compact('title', 'url', 'project_guid'),
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            $store['inputErrors']['url'][] = 'A valid URL is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $model = $this->monitoredUrlsApi->createModel();

        $model->title($title);
        $model->url($url);

        if ($project_guid) {
            $model->projectGuid($project_guid);
        }

        try {
            $this->monitoredUrlsApi->save($model);
        } catch (MonitoredUrlNameNotUniqueException $e) {
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
            'Monitored URL "' . $model->title() . '" created successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/monitored-urls/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
