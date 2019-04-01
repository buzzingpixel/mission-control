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
use src\app\notificationemails\exceptions\NotificationEmailNotUniqueException;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use const FILTER_VALIDATE_EMAIL;
use function filter_var;
use function trim;

class AddEmailNotificationAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var DataStoreInterface */
    private $dataStore;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;
    /** @var NotificationEmailsApiInterface */
    private $notificationEmailsApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper,
        NotificationEmailsApiInterface $notificationEmailsApi
    ) {
        $this->userApi               = $userApi;
        $this->response              = $response;
        $this->dataStore             = $dataStore;
        $this->flashDataApi          = $flashDataApi;
        $this->requestHelper         = $requestHelper;
        $this->notificationEmailsApi = $notificationEmailsApi;
    }

    /**
     * @throws Exception
     */
    public function __invoke() : ?ResponseInterface
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
            'inputValues' => ['email_address' => $emailAddress],
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

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

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
