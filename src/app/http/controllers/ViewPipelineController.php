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

        $isAdmin     = $user->getExtendedProperty('is_admin') === 1;
        $permissions = $user->userDataItem('permissions');
        $run         = $isAdmin ? true : $permissions['pipelines'][$model->guid()]['run'] ?? false;
        $edit        = $isAdmin ? true : $permissions['pipelines'][$model->guid()]['edit'] ?? false;

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

        $fetchParams = $this->pipelineApi->makeQueryModel();
        $fetchParams->addWhere('pipeline_guid', $model->getGuidAsBytes());
        $fetchParams->addWhere('is_finished', 0);
        $fetchParams->addWhere('has_failed', 0);
        $activeJob = $this->pipelineApi->fetchOneJob($fetchParams);

        if ($edit) {
            $pageControlButtons[] = [
                'href' => '/pipelines/edit/' . $model->slug(),
                'content' => 'Edit Pipeline',
            ];
        }

        if ($run) {
            $pageControlButtons[] = [
                'href' => '/pipelines/run/' . $model->slug(),
                'content' => 'Run Pipeline',
            ];
        }

        $innerComponentsHtml = '';

        if ($model->enableWebhook()) {
            $innerComponentsHtml = $this->twigEnvironment->renderAndMinify(
                'components/UrlTrigger.twig',
                [
                    'label' => 'Webhook Trigger',
                    'value' => getenv('SITE_URL') . '/pipelines/webhook/trigger/' . $model->guid(),
                ]
            );
        }

        $innerComponentsHtml .= ($this->renderPipelineInnerComponents)(
            $model
        );

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'notification' => $notification,
                'metaTitle' => $model->title(),
                'breadCrumbs' => $breadCrumbs,
                'title' => $model->title(),
                'subTitle' => $model->description(),
                'pageControlButtons' => $pageControlButtons,
                'innerComponentsHtml' => $innerComponentsHtml,
                'ajaxInnerRefreshUrl' => $activeJob ? '/pipelines/view/' . $model->slug() : null,
            ])
        );

        return $response;
    }
}
