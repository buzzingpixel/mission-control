<?php

declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\servers\interfaces\ServerApiInterface;

class CreatePipelines extends AbstractSeed
{
    /**
     * @return string[]
     */
    public function getDependencies() : array
    {
        return ['CreateServers'];
    }

    public function run() : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $serverApi = $di->get(ServerApiInterface::class);

        $serverQueryParams = $serverApi->makeQueryModel();
        $serverQueryParams->addWhere('title', 'buzzingpixel-do-utility-lemp');
        $server = $serverApi->fetchOne($serverQueryParams);

        /** @noinspection PhpUnhandledExceptionInspection */
        $projectApi = $di->get(ProjectsApiInterface::class);

        $projectQuery = $projectApi->makeQueryModel();

        $projectQuery->addWhere('title', 'Test Project 1');

        $project = $projectApi->fetchOne($projectQuery);

        /** @noinspection PhpUnhandledExceptionInspection */
        $pipelineApi = $di->get(PipelineApiInterface::class);

        $pipeline = $pipelineApi->createPipelineModel();

        $pipeline->title('TestPipeline');

        $pipeline->description('A test pipeline that will do stuff');

        $pipeline->projectGuid($project->guid());

        $item = $pipelineApi->createPipelineItemModel();

        $item->description('Test Item One');

        $item->script('echo -e "test item one was here" >> /home/buzzingpixel/testing/tmp.txt;');

        $item->addServer($server);

        $pipeline->addPipelineItem($item);

        $item = $pipelineApi->createPipelineItemModel();

        $item->description('Test Item Two');

        $item->script('echo -e "test item two was here" >> /home/buzzingpixel/testing/tmp.txt;');

        $item->addServer($server);

        $pipeline->addPipelineItem($item);

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            $pipelineApi->save($pipeline);
        } catch (Throwable $e) {
        }
    }
}
