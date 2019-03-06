<?php
declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\servers\ServerApi;
use src\app\projects\ProjectsApi;

class CreateServers extends AbstractSeed
{
    public function getDependencies()
    {
        return [
            'CreateProjects',
        ];
    }

    public function run()
    {
        $this->createServer('Test Server 1', '123.456.78.9');
        $this->createServer('Test Server 2', '987.654.32.1');
        $this->createServer('Test Server 3', '998.7.22.14');
    }

    private function createServer(string $title, string $addr)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $projectApi = $di->get(ProjectsApi::class);

        $projectQuery = $projectApi->makeQueryModel();

        $projectQuery->addWhere('title', 'Test Project 1');

        $project = $projectApi->fetchOne($projectQuery);

        /** @noinspection PhpUnhandledExceptionInspection */
        $serverApi = $di->get(ServerApi::class);

        $sshKeyQuery = $serverApi->makeQueryModel();

        $sshKeyQuery->addWhere('title', 'Test Key 1');

        $sshKeyModel = $serverApi->fetchOneSSHKey($sshKeyQuery);

        $model = $serverApi->createModel();

        $model->title($title);

        $model->address($addr);

        $model->sshPort(22);

        $model->sshUserName('test');

        $model->sshKeyModel($sshKeyModel);

        $model->projectGuid($project->guid());

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $serverApi->save($model);
        } catch (\Throwable $e) {
        }
    }
}