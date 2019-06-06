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
use src\app\pipelines\interfaces\PipelineApiInterface;
use Throwable;
use function md5;

class ViewPipelineJobDetailsControllerBadge
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

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        PipelineApiInterface $pipelineApi,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi             = $userApi;
        $this->response            = $response;
        $this->twigEnvironment     = $twigEnvironment;
        $this->pipelineApi         = $pipelineApi;
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

        $badgeUrl = '/pipelines/view/' . $pipelineModel->slug() . '/job-details/' . $jobModel->guid() . '/badge';

        $tags = [
            'content' => $status,
            'style' => $styledStatus,
            'ajaxRefreshTagUrl' => $jobModel->isFinished() || $jobModel->hasFailed() ? null : $badgeUrl,
            'ajaxRefreshTagUid' => md5($badgeUrl),
        ];

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('partials/Tags.twig', ['tags' => [$tags]])
        );

        return $response;
    }
}
