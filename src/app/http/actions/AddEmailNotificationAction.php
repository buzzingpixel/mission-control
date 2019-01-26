<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use src\app\notificationemails\exceptions\NotificationEmailNotUniqueException;

class AddEmailNotificationAction
{
    private $userApi;
    private $response;
    private $dataStore;
    private $flashDataApi;
    private $requestHelper;
    private $notificationEmailsApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper,
        NotificationEmailsApiInterface $notificationEmailsApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;
        $this->notificationEmailsApi = $notificationEmailsApi;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): ?ResponseInterface
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

        $emailAddress = trim($this->requestHelper->post('email_address'));

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'email_address' => $emailAddress,
            ],
        ];

        if (! filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            $store['inputErrors']['email_address'][] = 'A valid email address is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $model = $this->notificationEmailsApi->createModel();

        $model->emailAddress($emailAddress);

        try {
            $this->notificationEmailsApi->save($model);
        } catch (NotificationEmailNotUniqueException $e) {
            $store['inputErrors']['email_address'][] = 'Email address already exists';
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            $model->emailAddress() . ' added to notifications successfully!'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/admin');

        $response = $response->withStatus(303);

        return $response;
    }
}
