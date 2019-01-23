<?php
declare(strict_types=1);

namespace src\app\http\actions;

use Throwable;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\user\services\RegisterUserService;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class ChangePasswordAction
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
                'Change Password Action requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new Http404Exception();
        }

        $currentPassword = trim($this->requestHelper->post('current_password'));
        $newPassword = trim($this->requestHelper->post('new_password'));
        $confirmPassword = trim($this->requestHelper->post('confirm_password'));

        $store = [
            'inputErrors' => [],
        ];

        if (! $this->userApi->validateUserPassword($user->guid(), $currentPassword)) {
            $store['inputErrors']['current_password'][] = 'That is not the right password';
        }

        if (\strlen($newPassword) < RegisterUserService::MIN_PASSWORD_LENGTH) {
            $store['inputErrors']['new_password'][] = 'Your new password must be at least ' .
                RegisterUserService::MIN_PASSWORD_LENGTH . ' characters long';
        }

        if ($newPassword !== $confirmPassword) {
            $store['inputErrors']['confirm_password'][] = 'Your passwords did not match';
        }

        if ($store['inputErrors']) {
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        try {
            $this->userApi->setNewPassword($user, $newPassword);
        } catch (throwable $e) {
            $store['inputErrors']['current_password'][] = 'An unknown error occurred';
            $this->dataStore->storeItem('FormSubmission', $store);
            return null;
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Your password was updated successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/account');

        $response = $response->withStatus(303);

        return $response;
    }
}
