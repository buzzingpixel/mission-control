<?php

declare(strict_types=1);

namespace src\tests\servers\listeners;

use corbomite\db\models\QueryModel;
use PHPUnit\Framework\TestCase;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\listeners\ProjectUnArchiveListener;
use Throwable;

class ProjectUnArchiveListenerTest extends TestCase
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
            ->willReturn(true);

        $serverModelTwo = self::createMock(ServerModelInterface::class);

        $serverModelTwo->expects(self::at(0))
            ->method('isActive')
            ->willReturn(false);

        $serverModelTwo->expects(self::at(1))
            ->method('isActive')
            ->with(self::equalTo(true))
            ->willReturn(true);

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

        $event = self::createMock(ProjectBeforeUnArchiveEvent::class);

        $event->expects(self::once())
            ->method('projectModel')
            ->willReturn($projectModel);

        /** @noinspection PhpParamsInspection */
        $listener = new ProjectUnArchiveListener($serverApi);

        /** @noinspection PhpParamsInspection */
        $listener->call($event);
    }
}
