<?php

declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\pings\PingApi;
use src\app\projects\ProjectsApi;

class CreatePings extends AbstractSeed
{
    /**
     * @return string[]
     */
    public function getDependencies() : array
    {
        return ['CreateProjects'];
    }

    public function run() : void
    {
        $this->createPing('Test Ping 1', 2, 3);
        $this->createPing('Test Ping 2', 5, 6);
        $this->createPing('Test Ping 3', 1440, 60);
    }

    private function createPing(string $title, int $expect, int $warn) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $projectApi = $di->get(ProjectsApi::class);

        $projectQuery = $projectApi->makeQueryModel();

        $projectQuery->addWhere('title', 'Test Project 1');

        $project = $projectApi->fetchOne($projectQuery);

        /** @noinspection PhpUnhandledExceptionInspection */
        $pingApi = $di->get(PingApi::class);

        $model = $pingApi->createModel();

        $model->title($title);

        $model->expectEvery($expect);

        $model->warnAfter($warn);

        $model->projectGuid($project->guid());

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $pingApi->save($model);
        } catch (Throwable $e) {
        }
    }
}
