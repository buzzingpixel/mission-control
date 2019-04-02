<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use src\app\utilities\TimeZoneListUtility;
use Throwable;
use function date_default_timezone_get;

class AccountController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;
    /** @var TimeZoneListUtility */
    private $timeZoneListUtility;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService,
        TimeZoneListUtility $timeZoneListUtility
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
        $this->timeZoneListUtility = $timeZoneListUtility;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $requireLogin = $this->requireLoginService->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('Unknown Error');
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $tzs = $this->timeZoneListUtility->listTimeZones();

        $defaultTzContent = $tzs[date_default_timezone_get()] ??
            date_default_timezone_get();

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Your Account',
                'title' => 'Your Account',
                'pageControlButtons' => [
                    [
                        'href' => '/account/change-password',
                        'content' => 'Change Password',
                    ],
                ],
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'updateAccount',
                        'inputs' => [
                            [
                                'template' => 'Text',
                                'type' => 'email',
                                'name' => 'email',
                                'label' => 'Email Address',
                                'value' => $user->emailAddress(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'timezone',
                                'label' => 'Timezone',
                                'includeEmptyContent' => 'System Default (' .
                                    $defaultTzContent .
                                    ')',
                                'options' => $tzs,
                                'value' => $user->getExtendedProperty('timezone'),
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
