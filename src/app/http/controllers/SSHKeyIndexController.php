<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\servers\interfaces\ServerApiInterface;
use Throwable;

class SSHKeyIndexController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->serverApi           = $serverApi;
        $this->twigEnvironment     = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $requireLogin = $this->requireLoginService->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $archivesPage = $request->getAttribute('archives') === 'archives';

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('An unknown error occurred');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if (! $archivesPage) {
            $pageControlButtons[] = [
                'href' => '/ssh-keys/archives',
                'content' => 'View Archives',
            ];
        }

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/ssh-keys/create',
                'content' => 'Create SSH Key',
            ];
        }

        $params = $this->serverApi->makeQueryModel();
        $params->addOrder('title', 'asc');
        $params->addWhere('is_active', $archivesPage ? '0' : '1');

        $rows = [];

        foreach ($this->serverApi->fetchAllSSHKeys($params) as $model) {
            $rows[] = [
                'inputValue' => $model->guid(),
                'actionButtonLink' => '/ssh-keys/view/' . $model->slug(),
                'cols' => [
                    'Title' => $model->title(),
                ],
            ];
        }

        $actions = [];

        if ($isAdmin) {
            if ($archivesPage) {
                $actions['unArchive'] = 'Un-Archive Selected';
            }

            if (! $archivesPage) {
                $actions['archive'] = 'Archive Selected';
            }

            $actions['delete'] = 'Delete Selected';
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => $archivesPage ?
                    'SSH Key Archives' :
                    'SSH Keys',
                'breadCrumbs' => $archivesPage ? [
                    [
                        'href' => '/ssh-keys',
                        'content' => 'SSH Keys',
                    ],
                    ['content' => 'Viewing Archives'],
                ] : [],
                'title' => $archivesPage ?
                    'SSH Key Archives' :
                    'SSH Keys',
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'actionParam' => 'sshKeyListActions',
                        'actions' => $actions,
                        'actionColButtonContent' => 'View&nbsp;SSH Key&nbsp;Details',
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => ['Title'],
                            'rows' => $rows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
