<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\servers\interfaces\ServerApiInterface;
use Throwable;

class ServerManageAuthorizedKeys
{
    /** @var RequireLoginService */
    private $requireLogin;
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twig;

    public function __construct(
        RequireLoginService $requireLogin,
        ServerApiInterface $serverApi,
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twig
    ) {
        $this->requireLogin = $requireLogin;
        $this->serverApi    = $serverApi;
        $this->userApi      = $userApi;
        $this->response     = $response;
        $this->twig         = $twig;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $requireLogin = $this->requireLogin->requireLogin();

        if ($requireLogin) {
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

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('Unknown Error');
        }

        $isAdmin = $user->getExtendedProperty('is_admin') === 1;

        if (! $isAdmin) {
            throw new Http404Exception(
                'Server with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

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
            'href' => '/servers/view/' . $model->slug(),
            'content' => $model->title(),
        ];

        $breadCrumbs[] = ['content' => 'Authorized Keys'];

        $authorizedKeys = $this->serverApi->listServerAuthorizedKeys($model);

        $items = [];

        foreach ($authorizedKeys as $key) {
            $items[] = $key;
        }

        $response->getBody()->write(
            $this->twig->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => $model->title() . ' Authorized Keys',
                'title' => $model->title() . ' Authorized Keys',
                'breadCrumbs' => $breadCrumbs,
                'includes' => [
                    [
                        'template' => 'includes/AuthorizedKeys.twig',
                        'items' => $items,
                        'serverGuid' => $model->guid(),
                        'addActionParam' => 'addAuthorizedKeyToServer',
                        'removeActionParam' => 'removeAuthorizedKeyFromServer',
                        'returnUrl' => '/servers/authorized-keys/' . $model->slug(),
                    ],
                ],
            ])
        );

        return $response;
    }
}
