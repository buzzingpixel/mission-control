<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\interfaces\UserApiInterface;
use DateTime;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\reminders\exceptions\ReminderNameNotUniqueException;
use src\app\reminders\interfaces\ReminderApiInterface;
use function date_default_timezone_get;
use function trim;

class EditReminderAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var DataStoreInterface */
    private $dataStore;
    /** @var ReminderApiInterface */
    private $reminderApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        ReminderApiInterface $reminderApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->dataStore     = $dataStore;
        $this->reminderApi   = $reminderApi;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws Http404Exception
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Edit Reminder Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $fetchParams = $this->reminderApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->reminderApi->uuidToBytes(
            $this->requestHelper->post('guid')
        ));
        $model = $this->reminderApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception();
        }

        $title            = trim($this->requestHelper->post('title'));
        $message          = trim($this->requestHelper->post('message'));
        $startRemindingOn = trim($this->requestHelper->post('start_reminding_on'));
        $projectGuid      = trim($this->requestHelper->post('project_guid'));

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'title' => $title,
                'message' => $message,
                'start_reminding_on' => $startRemindingOn,
                'project_guid' => $projectGuid,
            ],
        ];

        if (! $title) {
            $store['inputErrors']['title'][] = 'This field is required';
        }

        $startRemindingOnDateTime = null;

        if (! $startRemindingOn) {
            $store['inputErrors']['start_reminding_on'][] = 'This field is required';
        }

        if ($startRemindingOn) {
            $startRemindingOnDateTime = DateTime::createFromFormat(
                'Y-m-d g:i:s a',
                $startRemindingOn . ' 12:00:00 am',
                new DateTimeZone(date_default_timezone_get())
            );
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $model->title($title);
        $model->message($message);
        $model->startRemindingOn($startRemindingOnDateTime);
        $model->projectGuid($projectGuid);

        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->reminderApi->save($model);
        } catch (ReminderNameNotUniqueException $e) {
            $store['inputErrors']['title'][] = 'Title must be unique';
            $this->dataStore->storeItem('FormSubmission', $store);

            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Reminder "' . $model->title() . '" saved successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader(
            'Location',
            '/reminders/view/' . $model->slug()
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
