<?php

declare(strict_types=1);

namespace src\tests\servers\listeners;

use corbomite\db\models\QueryModel;
use PHPUnit\Framework\TestCase;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\listeners\ProjectArchiveListener;
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

        $serverApi = self::createMock(ServerApiInterface::class);

        $serverApi->expects(self::once())
            ->method('makeQueryModel')
            ->willReturn($queryModel);

        $serverModelOne = self::createMock(ServerModelInterface::class);

        $serverModelOne->expects(self::once())
            ->method('isActive')
            ->willReturn(false);

        $serverModelTwo = self::createMock(ServerModelInterface::class);

        $serverModelTwo->expects(self::at(0))
            ->method('isActive')
            ->willReturn(true);

        $serverModelTwo->expects(self::at(1))
            ->method('isActive')
            ->with(self::equalTo(false))
            ->willReturn(false);

        $serverApi->expects(self::once())
            ->method('fetchAll')
            ->with(self::equalTo($queryModel))
            ->willReturn([
                $serverModelOne,
                $serverModelTwo,
            ]);

        $serverApi->expects(self::once())
            ->method('save')
            ->with(self::equalTo($serverModelTwo));

        $projectModel = self::createMock(ProjectModelInterface::class);

        $projectModel->expects(self::once())
            ->method('getGuidAsBytes')
            ->willReturn('GuidAsBytesReturn');

        $event = self::createMock(ProjectBeforeArchiveEvent::class);

        $event->expects(self::once())
            ->method('projectModel')
            ->willReturn($projectModel);

        /** @noinspection PhpParamsInspection */
        $listener = new ProjectArchiveListener($serverApi);

        /** @noinspection PhpParamsInspection */
        $listener->call($event);
    }
}
