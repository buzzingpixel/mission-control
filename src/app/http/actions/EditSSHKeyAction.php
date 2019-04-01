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
use src\app\servers\exceptions\TitleNotUniqueException;
use src\app\servers\interfaces\ServerApiInterface;
use function trim;

class EditSSHKeyAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var DataStoreInterface */
    private $dataStore;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        ServerApiInterface $serverApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->dataStore     = $dataStore;
        $this->serverApi     = $serverApi;
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
                'Edit SSH Key Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $fetchParams = $this->serverApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->serverApi->uuidToBytes(
            $this->requestHelper->post('guid')
        ));
        $model = $this->serverApi->fetchOneSSHKey($fetchParams);

        if (! $model) {
            throw new Http404Exception();
        }

        $title      = trim($this->requestHelper->post('title'));
        $regenerate = trim($this->requestHelper->post('regenerate') ?? '');
        $public     = trim($this->requestHelper->post('public'));
        $private    = trim($this->requestHelper->post('private'));

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'regenerate' => $regenerate,
                'public' => $public,
                'private' => $private,
            ],
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if ($regenerate !== 'true') {
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

        $model->title($title);
        $model->public($public);
        $model->private($private);

        if ($regenerate === 'true') {
            $key = $this->serverApi->generateSSHKey();
            $model->public($key['publickey']);
            $model->private($key['privatekey']);
        }

        try {
            $this->serverApi->saveSSHKey($model);
        } catch (TitleNotUniqueException $e) {
            $store['inputErrors']['title'][] = 'Title must be unique';
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'SSH Key "' . $model->title() . '" saved successfully.'
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
