<?php

declare(strict_types=1);

namespace src\app\http\services;

use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use Throwable;
use function date_default_timezone_get;
use function number_format;

class RenderPipelineInnerComponents
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var PipelineApiInterface */
    private $pipelineApi;

    public function __construct(
        UserApiInterface $userApi,
        TwigEnvironment $twigEnvironment,
        PipelineApiInterface $pipelineApi
    ) {
        $this->userApi         = $userApi;
        $this->twigEnvironment = $twigEnvironment;
        $this->pipelineApi     = $pipelineApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        PipelineModelInterface $pipelineModel,
        int $jobsLimit = 8
    ) : string {
        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('An unknown error occurred');
        }

        $rows = [];

        $params = $this->pipelineApi->makeQueryModel();
        $params->addOrder('job_added_at', 'desc');
        $params->addWhere('pipeline_guid', $pipelineModel->getGuidAsBytes());
        $params->limit($jobsLimit);

        $userTimeZone = $user->getExtendedProperty('timezone') ?:
            date_default_timezone_get();

        foreach ($this->pipelineApi->fetchAllJobs($params) as $model) {
            $model->jobAddedAt()->setTimezone(new DateTimeZone(
                $userTimeZone
            ));

            $status       = 'In queue';
            $styledStatus = 'Inactive';

            if ($model->hasFailed()) {
                $status       = 'Failed';
                $styledStatus = 'Error';
            } elseif ($model->isFinished()) {
                $status       = 'Finished';
                $styledStatus = 'Good';
            } elseif ($model->hasStarted()) {
                $status       = 'In progress';
                $styledStatus = 'Caution';
            }

            $rows[] = [
                'actionButtonLink' => '/pipelines/view/' . $pipelineModel->slug() . '/job-details/' . $model->guid(),
                'cols' => [
                    'Initiated' => $model->jobAddedAt()->format('n/j/Y g:i a'),
                    'Status' => $status,
                    'Percent Complete' => $status === 'Failed' || $status === 'Finished' ? '100%' :
                        number_format($model->percentComplete()) . '%',
                ],
                'colorStyledCols' => ['Status' => $styledStatus],
            ];
        }

        return $this->twigEnvironment->renderAndMinify('InnerComponentsAndLayouts.twig', [
            'includes' => [
                [
                    'template' => 'forms/TableListForm.twig',
                    'includeSelectCol' => false,
                    'includeFilter' => false,
                    'actionColButtonContent' => 'View&nbsp;Details',
                    'table' => [
                        'headings' => [
                            'Initiated',
                            'Status',
                            'Percent Complete',
                        ],
                        'rows' => $rows,
                    ],
                ],
            ],
        ]);
    }
}
