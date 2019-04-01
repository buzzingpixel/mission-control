<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\exceptions\Http500Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\servers\interfaces\ServerApiInterface;
use function count;

class SSHKeyListActions
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    /** @var array */
    private $guids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->serverApi     = $serverApi;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;

        $guids = $this->requestHelper->post('guids');

        if (! $guids) {
            return;
        }

        foreach ($guids as $guid) {
            $this->guids[] = $this->serverApi->uuidToBytes($guid);
        }
    }

    /**
     * @throws Http404Exception
     * @throws Http500Exception
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'SSH Key List Actions requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        if (! $this->guids) {
            throw new Http500Exception('No SSH Keys specified');
        }

        $fetchParams = $this->serverApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->guids);
        $models = $this->serverApi->fetchAllSSHKeys($fetchParams);

        $verb = '';

        foreach ($models as $model) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'archive':
                    $verb = 'archived';
                    $this->serverApi->archiveSSHKey($model);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->serverApi->deleteSSHKey($model);
                    break;
                case 'unArchive':
                    $verb = 'un-archived';
                    $this->serverApi->unArchiveSSHKey($model);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = count($models) > 1 ?
            'SSH Keys' :
            'SSH Key';

        $flashDataModel->dataItem(
            'content',
            $singularPlural . ' ' . $verb . ' successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/ssh-keys');

        $response = $response->withStatus(303);

        return $response;
    }
}
