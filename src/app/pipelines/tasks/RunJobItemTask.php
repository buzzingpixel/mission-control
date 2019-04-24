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
    public function __invoke(array $context = []) : void
    {
        $jobItemGuid = $context['jobItemGuid'] ?? '';

        if (! $jobItemGuid) {
            return;
        }

        $queryModel = $this->pipelineApi->makeQueryModel();
        $queryModel->addWhere('guid', $this->pipelineApi->uuidToBytes($jobItemGuid));
        $jobItemTmp = $this->pipelineApi->fetchOneJobItem($queryModel);

        if (! $jobItemTmp) {
            return;
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
            throw new LogicException('Something weird went wrong');
        }

        $job->hasStarted(true);

        $this->pipelineApi->saveJob($job);

        $exception = null;

        try {
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

            $jobItem->hasFailed(true);
            $job->hasFailed(true);

            foreach ($this->sendNotificationAdapters as $adapter) {
                $p = $job->pipeline();

                $sub = $msg = $job->pipeline()->title() . ' failed while running';

                $msg .= ". Details: \n\n";

                $msg .= $e->getMessage();

                $adapter->send($sub, $msg, [
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

        $this->pipelineApi->saveJob($job);

        if (! $exception) {
            return;
        }

        throw $exception;
    }

    private function innerRun(PipelineJobItemModelInterface $jobItem) : void
    {
        $pipelineItem = $jobItem->pipelineItem();

        $servers = $pipelineItem->servers();

        $task = $this;

        array_walk(
            $servers,
            static function (ServerModelInterface $server) use ($task, $jobItem) : void {
                $ssh = $task->getConnection->get($server);

                $jobItem->logContent((string) $ssh->exec($jobItem->getPreparedScriptForExecution()));

                $jobItem->finishedAt(new DateTime('now', new DateTimeZone('UTC')));

                $exitStatus = $ssh->getExitStatus();

                if ($exitStatus === false || $exitStatus === 0 || $exitStatus === '0') {
                    return;
                }

                throw new LogicException($jobItem->logContent());
            }
        );
    }
}
