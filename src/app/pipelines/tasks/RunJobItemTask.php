<?php

declare(strict_types=1);

namespace src\app\pipelines\tasks;

use DateTime;
use DateTimeZone;
use LogicException;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\services\GetLoggedInServerSshConnection;
use src\app\utilities\RSAFactory;
use src\app\utilities\SSH2Factory;
use Throwable;
use const PHP_EOL;
use function array_walk;
use function count;
use function getenv;

class RunJobItemTask
{
    /** @var PipelineApiInterface */
    private $pipelineApi;
    /** @var SSH2Factory */
    protected $ssh2Factory;
    /** @var RSAFactory */
    protected $rsaFactory;
    /** @var GetLoggedInServerSshConnection */
    private $getConnection;
    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    /**
     * @param SendNotificationAdapterInterface[] $sendNotificationAdapters
     */
    public function __construct(
        PipelineApiInterface $pipelineApi,
        SSH2Factory $ssh2Factory,
        RSAFactory $rsaFactory,
        GetLoggedInServerSshConnection $getConnection,
        array $sendNotificationAdapters = []
    ) {
        $this->pipelineApi              = $pipelineApi;
        $this->ssh2Factory              = $ssh2Factory;
        $this->rsaFactory               = $rsaFactory;
        $this->getConnection            = $getConnection;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    /**
     * @param mixed[] $context
     *
     * @throws Throwable
     */
    public function __invoke(array $context = []) : ?PipelineJobItemModelInterface
    {
        $failureCondition = isset($context['failureCondition']) && $context['failureCondition'] === true;

        $exception = null;

        try {
            $jobItemGuid = $context['jobItemGuid'] ?? '';

            if (! $jobItemGuid) {
                throw new LogicException('$context[\'jobItemGuid\'] does not exist');
            }

            $queryModel = $this->pipelineApi->makeQueryModel();
            $queryModel->addWhere('guid', $this->pipelineApi->uuidToBytes($jobItemGuid));
            $jobItemTmp = $this->pipelineApi->fetchOneJobItem($queryModel);

            if (! $jobItemTmp) {
                throw new LogicException('Unable to locate job');
            }

            $queryModel = $this->pipelineApi->makeQueryModel();
            $queryModel->addWhere('guid', $jobItemTmp->pipelineJob()->getGuidAsBytes());
            $job = $this->pipelineApi->fetchOneJob($queryModel);

            $jobItem = null;

            foreach ($job->pipelineJobItems() as $jobItemLoop) {
                if ($jobItemLoop->guid() !== $jobItemGuid) {
                    continue;
                }

                $jobItem = $jobItemLoop;

                break;
            }

            if (! $jobItem) {
                throw new LogicException('Something weird went wrong (unable to locate job, which is weird because we did above)');
            }

            if (! $failureCondition) {
                $job->hasStarted(true);

                $this->pipelineApi->saveJob($job);
            }

            $this->innerRun($jobItem);

            $totalJobs = count($job->pipelineJobItems());

            $jobsComplete = 0;

            foreach ($job->pipelineJobItems() as $thisJobItem) {
                if (! $thisJobItem->finishedAt()) {
                    continue;
                }

                $jobsComplete++;
            }

            $job->percentComplete((float) (($jobsComplete / $totalJobs) * 100));

            if ($jobsComplete >= $totalJobs) {
                $job->isFinished(true);
                $job->jobFinishedAt(new DateTime('now', new DateTimeZone('UTC')));
                $job->percentComplete((float) 100);

                foreach ($this->sendNotificationAdapters as $adapter) {
                    $p = $job->pipeline();

                    $msg = $p->title() . ' finished running successfully';

                    $adapter->send($msg, $msg, [
                        'status' => 'good',
                        'urls' => [
                            [
                                'content' => 'View Job Details',
                                'href' => getenv('SITE_URL') . '/pipelines/view/' . $p->slug() . '/job-details/' . $job->guid(),
                            ],
                        ],
                    ]);
                }
            }
        } catch (Throwable $e) {
            $exception = $e;

            if (isset($jobItem)) {
                $jobItem->hasFailed(true);
            }

            if (isset($job)) {
                $job->hasFailed(true);

                if (! $failureCondition) {
                    foreach ($job->pipelineJobItems() as $thisItem) {
                        if (isset($jobItem) && $thisItem->guid() === $jobItem->guid()) {
                            continue;
                        }

                        if ($thisItem->finishedAt() ||
                            ! $thisItem->pipelineItem()->runAfterFail()
                        ) {
                            continue;
                        }

                        $runJobItem = $this->__invoke([
                            'jobItemGuid' => $thisItem->guid(),
                            'failureCondition' => true,
                        ]);

                        if (! $runJobItem) {
                            continue;
                        }

                        $thisItem->finishedAt($runJobItem->finishedAt());
                        $thisItem->hasFailed($runJobItem->hasFailed());
                        $thisItem->logContent($runJobItem->logContent());
                    }
                }
            }

            foreach ($this->sendNotificationAdapters as $adapter) {
                if (! isset($job)) {
                    $subject = $msg = 'An error occurred while running a pipeline';

                    $msg .= ". Details: \n\n";

                    $msg .= $e->getMessage();

                    $adapter->send($subject, $msg, ['status' => 'bad']);

                    continue;
                }

                $p = $job->pipeline();

                $subject = $msg = $p->title() . ' failed while running';

                $msg .= ". Details: \n\n";

                $msg .= $e->getMessage();

                $adapter->send($subject, $msg, [
                    'status' => 'bad',
                    'urls' => [
                        [
                            'content' => 'View Job Details',
                            'href' => getenv('SITE_URL') . '/pipelines/view/' . $p->slug() . '/job-details/' . $job->guid(),
                        ],
                    ],
                ]);
            }
        }

        if (isset($job)) {
            $this->pipelineApi->saveJob($job);
        }

        if (! $exception) {
            return $jobItem ?? null;
        }

        throw $exception;
    }

    private function innerRun(PipelineJobItemModelInterface $jobItem) : void
    {
        $pipelineItem = $jobItem->pipelineItem();

        $servers = $pipelineItem->servers();

        if (! $servers) {
            $jobItem->logContent('No servers have been assigned to this item');
        }

        $task = $this;

        array_walk(
            $servers,
            static function (ServerModelInterface $server) use ($task, $jobItem) : void {
                $ssh = $task->getConnection->get($server);

                $prevLogContent = $jobItem->logContent();

                if ($prevLogContent) {
                    $prevLogContent .= PHP_EOL . PHP_EOL . '========================' . PHP_EOL . PHP_EOL;
                }

                $jobItem->logContent(
                    $prevLogContent .
                    'Running on ' . $server->title() . ':' . PHP_EOL .
                    (string) $ssh->exec($jobItem->getPreparedScriptForExecution())
                );

                $exitStatus = $ssh->getExitStatus();

                if ($exitStatus === false || $exitStatus === 0 || $exitStatus === '0') {
                    return;
                }

                throw new LogicException($jobItem->logContent());
            }
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $jobItem->finishedAt(new DateTime('now', new DateTimeZone('UTC')));
    }
}
