<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\utilities\TimeZoneListUtility;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;

class AccountController
{
    private $userApi;
    private $response;
    private $twigEnvironment;
    private $requireLoginService;
    private $timeZoneListUtility;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService,
        TimeZoneListUtility $timeZoneListUtility
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
        $this->timeZoneListUtility = $timeZoneListUtility;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        if (! $user = $this->userApi->fetchCurrentUser()) {
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
                            ]
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
