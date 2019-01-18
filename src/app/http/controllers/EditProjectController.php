<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\http\exceptions\Http404Exception;
use src\app\projects\interfaces\ProjectsApiInterface;

class EditProjectController
{
    private $userApi;
    private $response;
    private $projectsApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        ProjectsApiInterface $projectsApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->projectsApi = $projectsApi;
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

        $response = $this->response->withHeader('Content-Type', 'text/html');

        if (! $this->userApi->fetchCurrentUser()->userDataItem('admin')) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify('account/Unauthorized.twig')
            );

            return $response;
        }

        $fetchParams = $this->projectsApi->createFetchDataParams();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->projectsApi->fetchProject($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Project with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Edit Project: ' . $model->title(),
                'breadCrumbs' => [
                    [
                        'href' => '/projects',
                        'content' => 'Projects'
                    ],
                    [
                        'href' => '/projects/view/' . $model->slug(),
                        'content' => 'View'
                    ],
                    [
                        'content' => 'Edit'
                    ]
                ],
                'title' => 'Edit Project: ' . $model->title(),
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editProject',
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
                                'template' => 'TextArea',
                                'name' => 'description',
                                'label' => 'Description',
                                'value' => $model->description(),
                            ]
                        ],
                    ]
                ],
            ])
        );

        return $response;
    }
}
