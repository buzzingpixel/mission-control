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
use src\app\pings\interfaces\PingApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use Throwable;

class EditPingController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var PingApiInterface */
    private $pingApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        PingApiInterface $pingApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->pingApi             = $pingApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->projectsApi         = $projectsApi;
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

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('Unknown Error');
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        if ($user->getExtendedProperty('is_admin') !== 1) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify(
                    'account/Unauthorized.twig'
                )
            );

            return $response;
        }

        $fetchParams = $this->pingApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->pingApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Ping with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/pings',
                'content' => 'Pings',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Ping is archived';

            $breadCrumbs[] = [
                'href' => '/pings/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'href' => '/pings/view/' . $model->slug(),
            'content' => 'View',
        ];

        $breadCrumbs[] = ['content' => 'Edit'];

        $selectParams = $this->pingApi->makeQueryModel();
        $selectParams->addOrder('title', 'asc');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => 'Edit Ping: ' . $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => 'Edit Ping: ' . $model->title(),
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editPing',
                        'inputs' => [
                            [
                                'template' => 'Hidden',
                                'type' => 'hidden',
                                'name' => 'guid',
                                'value' => $model->guid(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'title',
                                'label' => 'Title',
                                'value' => $model->title(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'expect_every',
                                'label' => 'Expect Every (minutes)',
                                'value' => $model->expectEvery(),
                            ],
                            [
                                'template' => 'Text',
                                'type' => 'text',
                                'name' => 'warn_after',
                                'label' => 'Warn After (minutes)',
                                'value' => $model->warnAfter(),
                            ],
                            [
                                'template' => 'Select',
                                'name' => 'project_guid',
                                'label' => 'Project',
                                'options' => $this->projectsApi
                                    ->fetchAsSelectArray($selectParams),
                                'value' => $model->projectGuid(),
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
