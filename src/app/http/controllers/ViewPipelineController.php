<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RenderPipelineInnerComponents;
use src\app\http\services\RequireLoginService;
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;

class ViewPipelineController
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var PipelineApiInterface */
    private $pipelineApi;
    /** @var RequireLoginService */
    private $requireLoginService;
    /** @var RenderPipelineInnerComponents */
    private $renderPipelineInnerComponents;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        PipelineApiInterface $pipelineApi,
        RequireLoginService $requireLoginService,
        RenderPipelineInnerComponents $renderPipelineInnerComponents
    ) {
        $this->userApi                       = $userApi;
        $this->response                      = $response;
        $this->twigEnvironment               = $twigEnvironment;
        $this->pipelineApi                   = $pipelineApi;
        $this->requireLoginService           = $requireLoginService;
        $this->renderPipelineInnerComponents = $renderPipelineInnerComponents;
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

        $fetchParams = $this->pipelineApi->makeQueryModel();
        $fetchParams->addWhere('slug', $request->getAttribute('slug'));
        $model = $this->pipelineApi->fetchOne($fetchParams);

        if (! $model) {
            throw new Http404Exception(
                'Pipeline with slug "' . $request->getAttribute('slug') . '" not found'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
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

        $breadCrumbs[] = ['content' => 'Viewing'];

        $pageControlButtons = [];

        if ($isAdmin) {
            $pageControlButtons[] = [
                'href' => '/pipelines/edit/' . $model->slug(),
                'content' => 'Edit Pipeline',
            ];

            $pageControlButtons[] = [
                'href' => '/pipelines/run/' . $model->slug(),
                'content' => 'Run Pipeline',
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
                'innerComponentsHtml' => ($this->renderPipelineInnerComponents)(
                    $model
                ),
                'ajaxInnerRefreshUrl' => '/ajax/pipelines/view/' . $model->slug(),
            ])
        );

        return $response;
    }
}
