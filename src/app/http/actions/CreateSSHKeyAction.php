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
use src\app\servers\exceptions\TitleNotUniqueException;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class CreateSSHKeyAction
{
    private $userApi;
    private $response;
    private $dataStore;
    private $serverApi;
    private $flashDataApi;
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        ServerApiInterface $serverApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->serverApi = $serverApi;
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
                'Create SSH Key Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $title = trim($this->requestHelper->post('title'));
        $generate = trim($this->requestHelper->post('generate') ?? '');
        $public = trim($this->requestHelper->post('public'));
        $private = trim($this->requestHelper->post('private'));

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'generate' => $generate,
                'public' => $public,
                'private' => $private,
            ],
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if ($generate !== 'true') {
            if (! $public) {
                $store['inputErrors']['public'][] = 'This field is required';
            }

            if (! $private) {
                $store['inputErrors']['private'][] = 'This field is required';
            }
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $model = $this->serverApi->createSShKeyModel();

        $model->title($title);

        $model->public($public);
        $model->private($private);

        if ($generate === 'true') {
            // TODO: Generate SSH Keys
            dd('TODO: Generate SSH Keys');
        }

        try {
            $this->serverApi->saveSSHKey($model);
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
            'SSH Key "' . $model->title() . '" created successfully.'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/ssh-keys/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
