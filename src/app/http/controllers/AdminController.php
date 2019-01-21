<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;

class AdminController
{
    private $userApi;
    private $response;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
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

        if ($user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $rows = [];

        $queryModel = $this->userApi->makeQueryModel();
        $queryModel->addOrder('email_address', 'asc');
        $queryModel->addWhere('guid', $user->guid(), '!=');

        foreach ($this->userApi->fetchAll($queryModel) as $userModel) {
            $userIsAdmin = $userModel->getExtendedProperty('is_admin') === 1;
            $styledStatus = 'Inactive';

            if ($userIsAdmin) {
                $styledStatus = 'Good';
            }

            $rows[] = [
                'inputValue' => $userModel->guid(),
                'cols' => [
                    'Email' => $userModel->emailAddress(),
                    'Timezone' => $userModel->getExtendedProperty('timezone') ?: date_default_timezone_get(),
                    'Admin' => $userIsAdmin ? 'Yes' : 'No',
                ],
                'colorStyledCols' => [
                    'Admin' => $styledStatus,
                ],
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Admin',
                'title' => 'Admin',
                'subTitle' => 'Your user account is not shown',
                'pageControlButtons' => [
                    [
                        'href' => '/admin/create-user',
                        'content' => 'Create User',
                    ]
                ],
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'actionParam' => 'adminUserActions',
                        'actions' => [
                            'promote' => 'Promote Selected to Admin',
                            'demote' => 'Demote Selected from Admin',
                            'delete' => 'Delete Selected',
                        ],
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => [
                                'Email',
                                'Timezone',
                                'Admin'
                            ],
                            'rows' => $rows,
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
