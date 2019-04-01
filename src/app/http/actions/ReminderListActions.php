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
use src\app\reminders\interfaces\ReminderApiInterface;
use function count;

class ReminderListActions
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ReminderApiInterface */
    private $reminderApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    /** @var array */
    private $guids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ReminderApiInterface $reminderApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->reminderApi   = $reminderApi;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;

        $guids = $this->requestHelper->post('guids');

        if (! $guids) {
            return;
        }

        foreach ($guids as $guid) {
            $this->guids[] = $this->reminderApi->uuidToBytes($guid);
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
                'Ping List Actions requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        if (! $this->guids) {
            throw new Http500Exception('No Pings specified');
        }

        $fetchParams = $this->reminderApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->guids);
        $models = $this->reminderApi->fetchAll($fetchParams);

        $verb = '';

        foreach ($models as $model) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'archive':
                    $verb = 'archived';
                    $this->reminderApi->archive($model);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->reminderApi->delete($model);
                    break;
                case 'unArchive':
                    $verb = 'un-archived';
                    $this->reminderApi->unArchive($model);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = count($models) > 1 ?
            'Reminders' :
            'Reminder';

        $flashDataModel->dataItem(
            'content',
            $singularPlural . ' ' . $verb . ' successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/reminders');

        $response = $response->withStatus(303);

        return $response;
    }
}
