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
use src\app\pipelines\interfaces\PipelineApiInterface;

class ViewPipelineController
{
    private $userApi;
    private $response;
    private $pipelineApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        PipelineApiInterface $pipelineApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->pipelineApi = $pipelineApi;
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

        $fetchParams = $this->pipelineApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->pipelineApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Pipeline with slug "' . $request->getAttribute('slug') . '" not found'
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
                'href' => '/pipelines',
                'content' => 'Pipelines',
            ],
        ];

        if (! $model->isActive()) {
            $notification = 'This Pipeline is archived';

            $breadCrumbs[] = [
                'href' => '/pipelines/archives',
                'content' => 'Archives',
            ];
        }

        $breadCrumbs[] = [
            'content' => 'Viewing',
        ];

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/pipelines/edit/' . $model->slug(),
                'content' => 'Edit Pipeline',
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => $model->title(),
                'subTitle' => $model->description(),
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                ],
            ])
        );

        return $response;
    }
}
