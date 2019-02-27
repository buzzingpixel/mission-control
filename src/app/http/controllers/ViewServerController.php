<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use src\app\servers\interfaces\ServerApiInterface;

class ViewServerController
{
    private $userApi;
    private $response;
    private $serverApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ServerApiInterface $serverApi,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->serverApi = $serverApi;
        $this->twigEnvironment = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        $fetchParams = $this->serverApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->serverApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Server with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        if (! $user = $this->userApi->fetchCurrentUser()) {
            throw new LogicException('Unknown Error');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/servers',
                'content' => 'Servers',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Server is archived';

            $breadCrumbs[] = [
                'href' => '/servers/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'content' => 'Viewing',
        ];

        $key = $model->sshKeyModel();

        $keyValueItems = [
            [
                'key' => 'Title',
                'value' => $model->title(),
            ],
            [
                'key' => 'Address',
                'value' => $model->address(),
            ],
            [
                'key' => 'SSH Port',
                'value' => $model->sshPort(),
            ],
            [
                'key' => 'SSH User Name',
                'value' => $model->sshUserName(),
            ],
            [
                'key' => 'SSH Key',
                'value' => '<a href="/ssh-keys/view/' . $key->slug() . '">' . $key->title() . '</a>',
            ],
            [
                'key' => 'SSH Public Key',
                'value' => '<pre>' . $key->public() . '</pre>',
            ],
        ];

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/servers/edit/' . $model->slug(),
                'content' => 'Edit Server',
            ];

            $keyValueItems[] = [
                'key' => 'SSH Private Key',
                'value' => '<pre>' . $key->private() . '</pre>',
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => $model->title(),
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'includes/KeyValue.twig',
                        'keyValueItems' => $keyValueItems,
                    ],
                ],
            ])
        );

        return $response;
    }
}
