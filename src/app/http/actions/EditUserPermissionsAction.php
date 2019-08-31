<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\servers\interfaces\ServerApiInterface;
use Throwable;
use function is_array;

class EditUserPermissionsAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var PipelineApiInterface */
    private $pipelineApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        PipelineApiInterface $pipelineApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->serverApi     = $serverApi;
        $this->pipelineApi   = $pipelineApi;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Edit User Permissions Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $userToEdit = $this->userApi->fetchUser($this->requestHelper->post('user_guid'));

        if (! $userToEdit) {
            throw new Http404Exception();
        }

        $permissions = $this->requestHelper->post('permissions');

        $permissions = is_array($permissions) ? $permissions : [];

        foreach ($permissions as $permissionItem => $values) {
            foreach ($values as $id => $permissionItems) {
                foreach ($permissionItems as $item => $value) {
                    $permissions[$permissionItem][$id][$item] = $value === 'true';
                }
            }
        }

        $userToEdit->userDataItem('permissions', $permissions);

        $this->userApi->saveUser($userToEdit);

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            $userToEdit->emailAddress() . '" permissions updated.'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/admin'
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
