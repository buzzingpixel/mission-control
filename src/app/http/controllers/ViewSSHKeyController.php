<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use DateTimeZone;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use src\app\servers\interfaces\ServerApiInterface;

class ViewSSHKeyController
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
        $model = $this->serverApi->fetchOneSSHKey($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'SSH Key with slug "' . $request->getAttribute('slug') . '" not found'
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
                'href' => '/ssh-keys',
                'content' => 'SSH Keys',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This SSH Key is archived';

            $breadCrumbs[] = [
                'href' => '/ssh-keys/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'content' => 'Viewing',
        ];

        $keyValueItems = [
            [
                'key' => 'Public Key',
                'value' => '<pre>' . $model->public() . '</pre>',
            ],
        ];

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/ssk-keys/edit/' . $model->slug(),
                'content' => 'Edit SSH Key',
            ];

            $keyValueItems[] = [
                'key' => 'Private Key',
                'value' => '<pre>' . $model->private() . '</pre>',
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
