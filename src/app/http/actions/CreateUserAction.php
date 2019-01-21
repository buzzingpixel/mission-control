<?php
declare(strict_types=1);

namespace src\app\http\actions;

use Throwable;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\exceptions\UserExistsException;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class CreateUserAction
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
     * @throws Throwable
     */
    public function __invoke(): ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Create Project Action requires post request'
            );
        }

        if (! ($user = $this->userApi->fetchCurrentUser()) ||
            ($user->getExtendedProperty('is_admin') !== 1)
        ) {
            throw new Http404Exception();
        }

        $email = trim($this->requestHelper->getPost('email'));
        $admin = trim($this->requestHelper->getPost('admin') ?: '');

        $store = [
            'inputErrors' => [],
            'inputValues' => compact('email', 'admin'),
        ];

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $store['inputErrors']['email'][] = 'A valid email address is required';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        try {
            $this->userApi->registerUser($email, bin2hex(random_bytes(32)));
        } catch (UserExistsException $e) {
            $store['inputErrors']['email'][] = 'Email must be unique';
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        if ($admin === 'true') {
            if (!  $newUser = $this->userApi->fetchUser($email)) {
                throw new LogicException('An unknown error occurred');
            }

            $newUser->setExtendedProperty('is_admin', 1);

            $this->userApi->saveUser($newUser);
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem('content', 'User created successfully');

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/admin');

        $response = $response->withStatus(303);

        return $response;
    }
}
