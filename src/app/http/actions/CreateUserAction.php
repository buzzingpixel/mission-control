<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\exceptions\UserExistsException;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use const FILTER_VALIDATE_EMAIL;
use function bin2hex;
use function filter_var;
use function random_bytes;
use function trim;

class CreateUserAction
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

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        DataStoreInterface $dataStore,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->dataStore     = $dataStore;
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
                'Create User Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || ($user->getExtendedProperty('is_admin') !== 1)) {
            throw new Http404Exception();
        }

        $email = trim($this->requestHelper->getPost('email'));
        $admin = trim($this->requestHelper->getPost('admin') ?: '');

        $store = [
            'inputErrors' => [],
            'inputValues' => [
                'email' => $email,
                'admin' => $admin,
            ],
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
            $newUser = $this->userApi->fetchUser($email);

            if (! $newUser) {
                throw new LogicException('An unknown error occurred');
            }

            $newUser->setExtendedProperty('is_admin', 1);

            $this->userApi->saveUser($newUser);
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem('content', 'User created successfully');

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/admin');

        $response = $response->withStatus(303);

        return $response;
    }
}
