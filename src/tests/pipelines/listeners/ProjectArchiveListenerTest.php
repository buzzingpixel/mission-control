<?php

declare(strict_types=1);

namespace src\tests\pipelines\listeners;

use corbomite\db\models\QueryModel;
use PHPUnit\Framework\TestCase;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\listeners\ProjectArchiveListener;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use src\app\projects\interfaces\ProjectModelInterface;
use Throwable;

class ProjectArchiveListenerTest extends TestCase
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

        $pipelineApi = self::createMock(PipelineApiInterface::class);

        $pipelineApi->expects(self::once())
            ->method('makeQueryModel')
            ->willReturn($queryModel);

        $pipelineModelOne = self::createMock(PipelineModelInterface::class);

        $pipelineModelOne->expects(self::once())
            ->method('isActive')
            ->willReturn(false);

        $pipelineModelTwo = self::createMock(PipelineModelInterface::class);

        $pipelineModelTwo->expects(self::at(0))
            ->method('isActive')
            ->willReturn(true);

        $pipelineModelTwo->expects(self::at(1))
            ->method('isActive')
            ->with(self::equalTo(false))
            ->willReturn(false);

        $pipelineApi->expects(self::once())
            ->method('fetchAll')
            ->with(self::equalTo($queryModel))
            ->willReturn([
                $pipelineModelOne,
                $pipelineModelTwo,
            ]);

        $pipelineApi->expects(self::once())
            ->method('save')
            ->with(self::equalTo($pipelineModelTwo));

        $projectModel = self::createMock(ProjectModelInterface::class);

        $projectModel->expects(self::once())
            ->method('getGuidAsBytes')
            ->willReturn('GuidAsBytesReturn');

        $event = self::createMock(ProjectBeforeArchiveEvent::class);

        $event->expects(self::once())
            ->method('projectModel')
            ->willReturn($projectModel);

        /** @noinspection PhpParamsInspection */
        $listener = new ProjectArchiveListener($pipelineApi);

        /** @noinspection PhpParamsInspection */
        $listener->call($event);
    }
}
