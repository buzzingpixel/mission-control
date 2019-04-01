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
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use function count;

class NotificationEmailListActions
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;
    /** @var NotificationEmailsApiInterface */
    private $notificationEmailsApi;

    /** @var array */
    private $guids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper,
        NotificationEmailsApiInterface $notificationEmailsApi
    ) {
        $this->userApi               = $userApi;
        $this->response              = $response;
        $this->flashDataApi          = $flashDataApi;
        $this->requestHelper         = $requestHelper;
        $this->notificationEmailsApi = $notificationEmailsApi;

        $guids = $this->requestHelper->post('guids');

        if (! $guids) {
            return;
        }

        foreach ($guids as $guid) {
            $this->guids[] = $this->notificationEmailsApi->uuidToBytes($guid);
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
                'Notification Email List Actions requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        if (! $this->guids) {
            throw new Http500Exception('No Pings specified');
        }

        $fetchParams = $this->notificationEmailsApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->guids);
        $models = $this->notificationEmailsApi->fetchAll($fetchParams);

        $verb = '';

        foreach ($models as $model) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'disable':
                    $verb = 'disabled';
                    $this->notificationEmailsApi->disable($model);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->notificationEmailsApi->delete($model);
                    break;
                case 'enable':
                    $verb = 'enabled';
                    $this->notificationEmailsApi->enable($model);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = count($models) > 1 ?
            'Notification Emails' :
            'Notification Email';

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
