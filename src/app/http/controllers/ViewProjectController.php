<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;

class ViewProjectController
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

        $fetchParams = $this->projectsApi->createFetchDataParams();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->projectsApi->fetchProject($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Project with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $isAdmin = $this->userApi->fetchCurrentUser()->userDataItem('admin');

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/projects/edit/' . $model->slug(),
                'content' => 'Edit Project',
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => $model->title(),
                'breadCrumbs' => [
                    [
                        'href' => '/projects',
                        'content' => 'Projects'
                    ],
                    [
                        'content' => 'Viewing Project'
                    ]
                ],
                'title' => $model->title(),
                'subTitle' => $model->description(),
                'pageControlButtons' => $pageControlButtons,
            ])
        );

        return $response;
    }
}
