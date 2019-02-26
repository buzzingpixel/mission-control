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

class CreateServerAction
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
                'Create Server Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $title = trim($this->requestHelper->post('title'));
        $address = trim($this->requestHelper->post('address'));
        $sshPort = trim($this->requestHelper->post('ssh_port'));
        $sshUserName = trim($this->requestHelper->post('ssh_user_name'));
        $sshKeyGuid = trim($this->requestHelper->post('ssh_key_guid'));
        $projectGuid = trim($this->requestHelper->post('project_guid'));

        $sshKeyModel = null;

        if ($sshKeyGuid) {
            $fetchParams = $this->serverApi->makeQueryModel();
            $fetchParams->addWhere('guid', $this->serverApi->uuidToBytes(
                $sshKeyGuid
            ));
            $sshKeyModel = $this->serverApi->fetchOneSSHKey($fetchParams);
        }

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'address' => $address,
                'ssh_port' => $sshPort,
                'ssh_user_name' => $sshUserName,
                'ssh_key_guid' => $sshKeyGuid,
                'project_guid' => $projectGuid,
            ],
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        if (! $address) {
            $store['inputErrors']['address'][] = 'This field is required';
        }

        if (! ctype_digit($sshPort)) {
            $store['inputErrors']['ssh_port'][] = 'Must be a whole number';
        }

        if (! $sshUserName) {
            $store['inputErrors']['ssh_user_name'][] = 'This field is required';
        }

        if (! $sshKeyGuid) {
            $store['inputErrors']['ssh_key_guid'][] = 'This field is required';
        } elseif (! $sshKeyModel) {
            $store['inputErrors']['ssh_key_guid'][] = 'A valid SSH Key is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $model = $this->serverApi->createModel();

        $model->title($title);
        $model->address($address);
        $model->sshPort((int) $sshPort);
        $model->sshUserName($sshUserName);
        $model->sshKeyModel($sshKeyModel);

        if ($projectGuid) {
            $model->projectGuid($projectGuid);
        }

        try {
            $this->serverApi->save($model);
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
            'Server "' . $model->title() . '" created successfully.'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/servers/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
