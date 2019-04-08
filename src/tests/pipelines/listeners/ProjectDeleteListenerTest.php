<?php

declare(strict_types=1);

namespace src\tests\pipelines\listeners;

use corbomite\db\models\QueryModel;
use PHPUnit\Framework\TestCase;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\listeners\ProjectDeleteListener;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\projects\interfaces\ProjectModelInterface;
use Throwable;

class ProjectDeleteListenerTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCall() : void
    {
        $queryModel = self::createMock(QueryModel::class);

        $queryModel->expects(self::once())
            ->method('addWhere')
            ->with(
                self::equalTo('project_guid'),
                self::equalTo('GuidAsBytesReturn')
            );

        $pipelineModelOne = self::createMock(PipelineModelInterface::class);

        $pipelineModelTwo = self::createMock(PipelineModelInterface::class);

        $pipelineApi = self::createMock(PipelineApiInterface::class);

        $pipelineApi->expects(self::at(0))
            ->method('makeQueryModel')
            ->willReturn($queryModel);

        $pipelineApi->expects(self::at(1))
            ->method('fetchAll')
            ->with(self::equalTo($queryModel))
            ->willReturn([
                $pipelineModelOne,
                $pipelineModelTwo,
            ]);

        $pipelineApi->expects(self::at(2))
            ->method('delete')
            ->with(self::equalTo($pipelineModelOne));

        $pipelineApi->expects(self::at(3))
            ->method('delete')
            ->with(self::equalTo($pipelineModelTwo));

        $projectModel = self::createMock(ProjectModelInterface::class);

        $projectModel->expects(self::once())
            ->method('getGuidAsBytes')
            ->willReturn('GuidAsBytesReturn');

        $event = self::createMock(ProjectBeforeDeleteEvent::class);

        $event->expects(self::once())
            ->method('projectModel')
            ->willReturn($projectModel);

        /** @noinspection PhpParamsInspection */
        $listener = new ProjectDeleteListener($pipelineApi);

        /** @noinspection PhpParamsInspection */
        $listener->call($event);
    }
}
