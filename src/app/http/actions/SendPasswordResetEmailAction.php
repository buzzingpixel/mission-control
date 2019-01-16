<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use corbomite\user\interfaces\UserApiInterface;
use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;

class SendPasswordResetEmailAction
{
    private $userApi;
    private $response;
    private $emailApi;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        EmailApiInterface $emailApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->emailApi = $emailApi;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $requestMethod = strtolower(
            $request->getServerParams()['REQUEST_METHOD'] ?? 'get'
        );

        if ($requestMethod !== 'post') {
            throw new LogicException(
                'Send Password Reset Action requires post request'
            );
        }

        $emailAddress = (string) ($request->getParsedBody()['email'] ?? '');

        if (! $user = $this->userApi->fetchUser($emailAddress)) {
            return $this->redirect();
        }

        $token = $this->userApi->generatePasswordResetToken($user);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->emailApi->addEmailToQueue($this->emailApi->createEmailModel([
            'toEmail' => $user->emailAddress(),
            'subject' => 'Your Mission Control Password Reset Request',
            'messageHtml' => 'Someone requested a password reset link ' .
                'for your Mission Control account. If that was you, click ' .
                'this link:<br><br><a href="' .
                getenv('SITE_URL') . '/iforgot/reset/' . $token . '">' .
                'Reset your password</a><br><br>' .
                "If that wasn't you, you can ignore this email",
        ]));

        return $this->redirect();
    }

    private function redirect(): ResponseInterface
    {
        $response = $this->response->withHeader(
            'Location',
            '/iforgot/check-email'
        );

        $response = $response->withStatus(303);

        return $response;
    }
}
