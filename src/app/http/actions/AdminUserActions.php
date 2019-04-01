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
use Throwable;
use function count;

class AdminUserActions
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    /** @var mixed */
    private $guids;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;

        $this->guids = $this->requestHelper->post('guids');
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Create Project Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || ($user->getExtendedProperty('is_admin') !== 1)) {
            throw new Http404Exception();
        }

        if (! $this->guids) {
            throw new Http500Exception('No projects specified');
        }

        $queryModel = $this->userApi->makeQueryModel();
        $queryModel->addWhere('guid', $this->guids);
        $users = $this->userApi->fetchAll($queryModel);

        $verb = '';

        foreach ($users as $userModel) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'promote':
                    $verb = 'promoted';
                    $userModel->setExtendedProperty('is_admin', 1);
                    $this->userApi->saveUser($userModel);
                    break;
                case 'demote':
                    $verb = 'demoted';
                    $userModel->setExtendedProperty('is_admin', 0);
                    $this->userApi->saveUser($userModel);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->userApi->deleteUser($userModel);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = count($users) > 1 ? 'Users' : 'User';

        $flashDataModel->dataItem(
            'content',
            $singularPlural . ' ' . $verb . ' successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/admin');

        $response = $response->withStatus(303);

        return $response;
    }
}
