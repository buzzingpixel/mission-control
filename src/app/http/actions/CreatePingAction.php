<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\interfaces\UserApiInterface;
use Exception;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\pings\exceptions\PingNameNotUniqueException;
use src\app\pings\interfaces\PingApiInterface;
use function ctype_digit;
use function trim;

class CreatePingAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var PingApiInterface */
    private $pingApi;
    /** @var ResponseInterface */
    private $response;
    /** @var DataStoreInterface */
    private $dataStore;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        PingApiInterface $pingApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->pingApi       = $pingApi;
        $this->response      = $response;
        $this->dataStore     = $dataStore;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws Exception
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Create Ping Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $title       = trim($this->requestHelper->post('title'));
        $expectEvery = trim($this->requestHelper->post('expect_every'));
        $warnAfter   = trim($this->requestHelper->post('warn_after'));
        $projectGuid = trim($this->requestHelper->post('project_guid'));

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'expect_every' => $expectEvery,
                'warn_after' => $warnAfter,
                'project_guid' => $projectGuid,
            ],
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if (! ctype_digit($expectEvery)) {
            $store['inputErrors']['expect_every'][] = 'Must be a whole number';
        }

        if (! ctype_digit($warnAfter)) {
            $store['inputErrors']['warn_after'][] = 'Must be a whole number';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $model = $this->pingApi->createModel();

        $model->title($title);
        $model->expectEvery((int) $expectEvery);
        $model->warnAfter((int) $warnAfter);
        $model->projectGuid($projectGuid);

        try {
            $this->pingApi->save($model);
        } catch (PingNameNotUniqueException $e) {
            $store['inputErrors']['title'][] = 'Title must be unique';
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Ping "' . $model->title() . '" created successfully.'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/pings/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
