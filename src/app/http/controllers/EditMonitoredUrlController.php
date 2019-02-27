<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\http\exceptions\Http404Exception;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class EditMonitoredUrlController
{
    private $userApi;
    private $response;
    private $projectsApi;
    private $twigEnvironment;
    private $monitoredUrlsApi;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->projectsApi = $projectsApi;
        $this->twigEnvironment = $twigEnvironment;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
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

        if (! $user = $this->userApi->fetchCurrentUser()) {
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

        $fetchParams = $this->monitoredUrlsApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->monitoredUrlsApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Monitored URL with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $notification = false;

        $breadCrumbs = [
            [
                'href' => '/monitored-urls',
                'content' => 'Monitored URLs',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Monitored URL is archived';

            $breadCrumbs[] = [
                'href' => '/monitored-urls/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'href' => '/monitored-urls/view/' . $model->slug(),
            'content' => 'View',
        ];

        $breadCrumbs[] = [
            'content' => 'Edit'
        ];

        $selectParams = $this->projectsApi->makeQueryModel();
        $selectParams->addOrder('title', 'asc');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => 'Edit Monitored URL: ' . $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => 'Edit Monitored URL: ' . $model->title(),
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editMonitoredUrl',
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
                                'name' => 'url',
                                'label' => 'URL',
                                'value' => $model->url(),
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
                    ]
                ],
            ])
        );

        return $response;
    }
}
