<?php
declare(strict_types=1);

namespace src\app\http\actions;

use Throwable;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class UpdateAccountAction
{
    private $userApi;
    private $response;
    private $dataStore;
    private $flashDataApi;
    private $requestHelper;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->dataStore = $dataStore;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @throws Http404Exception
     */
    public function __invoke(): ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Update Account Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new Http404Exception();
        }

        $email = trim($this->requestHelper->post('email'));
        $timezone = trim($this->requestHelper->post('timezone'));

        $store = [
            'inputErrors' => [],
            'inputValues' => compact('email', 'timezone'),
        ];

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $store['inputErrors']['email'][] = 'A valid email address is required';
        }

        if ($timezone) {
            try {
                new DateTimeZone($timezone);
            } catch (Throwable $e) {
                $store['inputErrors']['timezone'][] = 'A valid timezone string is required';
            }
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $user->emailAddress($email);

        $user->setExtendedProperty('timezone', $timezone);

        try {
            $this->userApi->saveUser($user);
        } catch (throwable $e) {
            $store['inputErrors']['title'][] = 'An unknown error occurred';
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Account settings saved successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/projects');

        $response = $response->withStatus(303);

        return $response;
    }
}
