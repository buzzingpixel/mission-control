<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RenderPipelineInnerComponents;
use src\app\http\services\RequireLoginService;
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;
use function date_default_timezone_get;

class ViewPipelineJobDetailsController
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

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('An unknown error occurred');
        }

        $pipelineSlug = $request->getAttribute('slug');

        $params = $this->pipelineApi->makeQueryModel();
        $params->addWhere('slug', $pipelineSlug);
        $pipelineModel = $this->pipelineApi->fetchOne($params);

        if (! $pipelineModel) {
            throw new Http404Exception(
                'Pipeline with slug "' . $pipelineSlug . '" not found'
            );
        }

        $jobGuid = $request->getAttribute('guid');

        $params = $this->pipelineApi->makeQueryModel();
        $params->addWhere('pipeline_guid', $pipelineModel->getGuidAsBytes());
        $params->addWhere('guid', $this->pipelineApi->uuidToBytes($jobGuid));

        $jobModel = $this->pipelineApi->fetchOneJob($params);

        if (! $jobModel) {
            throw new Http404Exception(
                'Pipeline job with guid "' . $jobGuid . '" not found'
            );
        }

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        $jobModel->jobAddedAt()->setTimezone(new DateTimeZone($userTimeZone));

        $status       = 'In queue';
        $styledStatus = 'Inactive';

        if ($jobModel->hasFailed()) {
            $status       = 'Failed';
            $styledStatus = 'Error';
        } elseif ($jobModel->isFinished()) {
            $status       = 'Finished';
            $styledStatus = 'Good';
        } elseif ($jobModel->hasStarted()) {
            $status       = 'In progress';
            $styledStatus = 'Caution';
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $title = 'Pipeline "' . $pipelineModel->title() . '" Job at ' . $jobModel->jobAddedAt()->format('n/j/Y g:i a');

        $rows = [];

        foreach ($jobModel->pipelineJobItems() as $jobItem) {
            if ($jobItem->finishedAt()) {
                $jobItem->finishedAt()->setTimezone(new DateTimeZone($userTimeZone));
            }

            $status       = 'In queue';
            $styledStatus = 'Inactive';

            if ($jobItem->hasFailed()) {
                $status       = 'Failed';
                $styledStatus = 'Error';
            } elseif ($jobItem->finishedAt()) {
                $status       = 'Finished';
                $styledStatus = 'Good';
            }

            $rows[] = [
                'cols' => [
                    'Description' => $jobItem->pipelineItem()->description(),
                    'Status' => $status,
                    'Finished At' => $jobItem->finishedAt() ? $jobItem->finishedAt()->format('n/j/Y g:i a') : '',
                    'Log' => $jobItem->logContent(),
                ],
                'colorStyledCols' => ['Status' => $styledStatus],
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'tags' => [[
                    'content' => $status,
                    'style' => $styledStatus,
                ],
                ],
                'metaTitle' => $title,
                'title' =>$title,
                'breadCrumbs' => [
                    [
                        'href' => '/pipelines',
                        'content' => 'Pipelines',
                    ],
                    [
                        'href' => '/pipelines/view/' . $pipelineSlug,
                        'content' => $pipelineModel->title(),
                    ],
                    ['content' => 'Viewing Job'],
                ],
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'includeSelectCol' => false,
                        'includeFilter' => false,
                        'table' => [
                            'headings' => [
                                'Description',
                                'Status',
                                'Finished At',
                                'Log',
                            ],
                            'rows' => $rows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
