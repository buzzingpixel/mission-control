<?php

declare(strict_types=1);

namespace src\app\pipelines\tasks;

use DateTime;
use DateTimeZone;
use LogicException;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\services\GetLoggedInServerSshConnection;
use src\app\utilities\RSAFactory;
use src\app\utilities\SSH2Factory;
use Symfony\Component\Yaml\Yaml;
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
    /** @var ServerApiInterface */
    private $serverApi;
    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    private $activeServerConnections = [];

    /**
     * @param SendNotificationAdapterInterface[] $sendNotificationAdapters
     */
    public function __construct(
        PipelineApiInterface $pipelineApi,
        SSH2Factory $ssh2Factory,
        RSAFactory $rsaFactory,
        GetLoggedInServerSshConnection $getConnection,
        ServerApiInterface $serverApi,
        array $sendNotificationAdapters = []
    ) {
        $this->pipelineApi              = $pipelineApi;
        $this->ssh2Factory              = $ssh2Factory;
        $this->rsaFactory               = $rsaFactory;
        $this->getConnection            = $getConnection;
        $this->serverApi = $serverApi;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    /**
     * @param mixed[] $context
     *
     * @throws Throwable
     */
    public function __invoke(array $context = []) : ?PipelineJobItemModelInterface
    {
        $failureCondition = isset($context['failureCondition']) &&
            $context['failureCondition'] === true;

        $exception = null;

        try {
            $jobItemGuid = $context['jobItemGuid'] ?? '';

            if (! $jobItemGuid) {
                throw new LogicException(
                    '$context[\'jobItemGuid\'] does not exist'
                );
            }

            $queryModel = $this->pipelineApi->makeQueryModel();

            $queryModel->addWhere(
                'guid',
                $this->pipelineApi->uuidToBytes($jobItemGuid)
            );

            $jobItemTmp = $this->pipelineApi->fetchOneJobItem(
                $queryModel
            );

            if (! $jobItemTmp) {
                throw new LogicException('Unable to locate job');
            }

            $queryModel = $this->pipelineApi->makeQueryModel();

            $queryModel->addWhere(
                'guid',
                $jobItemTmp->pipelineJob()->getGuidAsBytes()
            );

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
                throw new LogicException(
                    'Something weird went wrong (unable to locate ' .
                    'job, which is weird because we did above)'
                );
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

            $job->percentComplete(
                (float) (($jobsComplete / $totalJobs) * 100)
            );

            if ($jobsComplete >= $totalJobs) {
                $job->isFinished(true);

                $job->jobFinishedAt(new DateTime(
                    'now',
                    new DateTimeZone('UTC')
                ));

                $job->percentComplete((float) 100);

                foreach ($this->sendNotificationAdapters as $adapter) {
                    $p = $job->pipeline();

                    $msg = $p->title() . ' finished running successfully';

                    $adapter->send($msg, $msg, [
                        'status' => 'good',
                        'urls' => [
                            [
                                'content' => 'View Job Details',
                                'href' => getenv('SITE_URL') .
                                    '/pipelines/view/' .
                                    $p->slug() .
                                    '/job-details/' .
                                    $job->guid(),
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
                        if (isset($jobItem) &&
                            $thisItem->guid() === $jobItem->guid()
                        ) {
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

                    $adapter->send(
                        $subject,
                        $msg,
                        ['status' => 'bad']
                    );

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
                            'href' => getenv('SITE_URL') .
                                '/pipelines/view/' .
                                $p->slug() .
                                '/job-details/' .
                                $job->guid(),
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
            $jobItem->logContent(
                'No servers have been assigned to this item'
            );
        }

        $task = $this;

        array_walk(
            $servers,
            static function (ServerModelInterface $server) use (
                $task,
                $jobItem
            ) : void {
                switch ($jobItem->pipelineItem()->type()) {
                    case 'source':
                        $task->runFromSource($jobItem, $server);

                        return;
                    case 'code':
                        $task->runCode($jobItem, $server);

                        return;
                    default:
                        throw new LogicException(
                            'Type not implemented'
                        );
                }
            }
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $jobItem->finishedAt(new DateTime(
            'now',
            new DateTimeZone('UTC')
        ));
    }

    private function runFromSource(
        PipelineJobItemModelInterface $jobItem,
        ServerModelInterface $server
    ) : void {
        $jobItem->pipelineItem()->script(
            'cat ' . $jobItem->pipelineItem()->script()
        );

        if (isset($this->activeServerConnections[$server->slug()])) {
            $ssh = $this->activeServerConnections[$server->slug()];
        } else {
            $ssh = $this->getConnection->get($server);

            $this->activeServerConnections[$server->slug()] = $ssh;
        }

        $logContent = $jobItem->logContent();

        if ($logContent) {
            $logContent .= PHP_EOL . PHP_EOL .
                '========================' . PHP_EOL . PHP_EOL;
        }

        $logContent .= 'Running on ' . $server->title() . PHP_EOL .
            'Script:' . PHP_EOL . '```' . PHP_EOL .
            $jobItem->getPreparedScriptForExecution() . PHP_EOL . '```' . PHP_EOL;

        $jobItem->logContent($logContent);

        $this->pipelineApi->saveJob($jobItem->pipelineJob());

        try {
            $yaml = Yaml::parse($ssh->exec(
                $jobItem->getPreparedScriptForExecution()
            ));
        } catch (Throwable $e) {
            $msg = 'There was a problem parsing the YAML input';

            $logContent .= $msg;

            $jobItem->logContent($logContent);

            $this->pipelineApi->saveJob($jobItem->pipelineJob());

            throw new LogicException($msg, 0, $e);
        }

        $yamlPipelineItems = $yaml['pipelineItems'] ?? null;

        if (! is_array($yamlPipelineItems)) {
            $msg = 'The key \'pipelineItems\' was not found in YAML ' .
                'file or was not an array';

            $logContent .= $msg;

            $jobItem->logContent($logContent);

            $this->pipelineApi->saveJob($jobItem->pipelineJob());

            throw new LogicException($msg);
        }

        $runBeforeEveryItem = $jobItem->pipeline()->runBeforeEveryItem();

        if ($runBeforeEveryItem !== '') {
            $runBeforeEveryItem .= PHP_EOL;
        }

        $runBeforeEveryItem .= $yaml['runBeforeEveryItem'] ?? '';

        $counter = 0;

        foreach ($yamlPipelineItems as $sourcedPipelineItem) {
            $counter++;

            $desc = 'YAML Pipeline Item ' . $counter;

            $yamlDesc = $sourcedPipelineItem['description'] ?? '';

            if ($yamlDesc !== '') {
                $desc .= ': ' . $yamlDesc;
            }

            if (! isset($sourcedPipelineItem['script']) ||
                ! is_string($sourcedPipelineItem['script'])
            ) {
                $msg = 'Script key not found on YAML pipeline item';

                $logContent .= $msg;

                $jobItem->logContent($logContent);

                $this->pipelineApi->saveJob($jobItem->pipelineJob());

                throw new LogicException($msg);
            }

            $script = $this->prepareForExecution(
                $sourcedPipelineItem,
                $jobItem,
                $runBeforeEveryItem
            );

            $logContent .= PHP_EOL . PHP_EOL .
                '========================' . PHP_EOL . PHP_EOL;

            $logContent .= $desc . PHP_EOL;

            $jobItem->logContent($logContent);

            $this->pipelineApi->saveJob($jobItem->pipelineJob());

            $servers = $sourcedPipelineItem['runOnServers'] ?? [];

            if (count($servers) < 1) {
                $serverModels = [$server];
            } else {
                $serverQuery = $this->serverApi->makeQueryModel();

                $serverQuery->addWhere(
                    'slug',
                    $servers,
                    'IN'
                );

                $serverModels = $this->serverApi->fetchAll(
                    $serverQuery
                );
            }

            if (count($serverModels) < 1) {
                $logContent .= 'No servers found';

                $jobItem->logContent($logContent);

                $this->pipelineApi->saveJob($jobItem->pipelineJob());

                continue;
            }

            foreach ($serverModels as $serverModel) {
                if (isset($this->activeServerConnections[$serverModel->slug()])) {
                    $serverSsh = $this->activeServerConnections[$serverModel->slug()];
                } else {
                    $serverSsh = $this->getConnection->get($serverModel);

                    $this->activeServerConnections[$server->slug()] = $serverSsh;
                }

                $logContent .= 'Running on ' .
                    $serverModel->title() . PHP_EOL . 'Script: ' . PHP_EOL . '```' . PHP_EOL .
                    $script . PHP_EOL . '```' . PHP_EOL;

                $jobItem->logContent($logContent);

                $this->pipelineApi->saveJob($jobItem->pipelineJob());

                $logContent .= (string) $serverSsh->exec($script);

                $jobItem->logContent($logContent);

                $this->pipelineApi->saveJob($jobItem->pipelineJob());

                $exitStatus = $ssh->getExitStatus();

                if ($exitStatus === false || $exitStatus === 0 || $exitStatus === '0') {
                    continue;
                }

                throw new LogicException($jobItem->logContent());
            }
        }
    }

    private function prepareForExecution(
        array $sourcedPipelineItem,
        PipelineJobItemModelInterface $jobItem,
        string $runBeforeEveryItem
    ) : string {
        $dateTime = $jobItem->pipelineJob()->jobAddedAt();

        // Do {{timestamp}} replacement
        $preparedString = str_replace(
            '{{timestamp}}',
            $dateTime->getTimestamp(),
            $runBeforeEveryItem . PHP_EOL . $sourcedPipelineItem['script']
        );

        // Find instances of {{time "FORMAT_HERE"}} or {{time 'FORMAT_HERE'}}
        preg_match_all(
            '/{{time (?:"|\')(.+?)(?:"|\')}}/',
            $preparedString,
            $timeMatches,
            PREG_SET_ORDER
        );

        // Do replacements
        foreach ($timeMatches as $match) {
            $replacement = $dateTime->format($match[1]);

            $preparedString = preg_replace(
                '/' . $match[0] . '/',
                $replacement,
                $preparedString,
                1
            );
        }

        return trim($preparedString);
    }

    private function runCode(
        PipelineJobItemModelInterface $jobItem,
        ServerModelInterface $server
    ) {
        if (isset($this->activeServerConnections[$server->slug()])) {
            $ssh = $this->activeServerConnections[$server->slug()];
        } else {
            $ssh = $this->getConnection->get($server);

            $this->activeServerConnections[$server->slug()] = $ssh;
        }

        $logContent = $jobItem->logContent();

        if ($logContent) {
            $logContent .= PHP_EOL . PHP_EOL .
                '========================' . PHP_EOL . PHP_EOL;
        }

        $logContent .= 'Running on ' . $server->title() . PHP_EOL .
            'Script:' . PHP_EOL . '```' . PHP_EOL .
            $jobItem->getPreparedScriptForExecution() . PHP_EOL . '```' . PHP_EOL;

        $jobItem->logContent($logContent);

        $this->pipelineApi->saveJob($jobItem->pipelineJob());

        $logContent .= (string) $ssh->exec(
            $jobItem->getPreparedScriptForExecution()
        );

        $jobItem->logContent($logContent);

        $this->pipelineApi->saveJob($jobItem->pipelineJob());

        $exitStatus = $ssh->getExitStatus();

        if ($exitStatus === false || $exitStatus === 0 || $exitStatus === '0') {
            return;
        }

        throw new LogicException($jobItem->logContent());
    }
}
