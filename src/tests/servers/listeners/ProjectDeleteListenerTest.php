<?php

declare(strict_types=1);

namespace src\tests\servers\listeners;

use corbomite\db\models\QueryModel;
use PHPUnit\Framework\TestCase;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\listeners\ProjectDeleteListener;
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

        $serverModelOne = self::createMock(ServerModelInterface::class);

        $serverModelTwo = self::createMock(ServerModelInterface::class);

        $serverApi = self::createMock(ServerApiInterface::class);

        $serverApi->expects(self::at(0))
            ->method('makeQueryModel')
            ->willReturn($queryModel);

        $serverApi->expects(self::at(1))
            ->method('fetchAll')
            ->with(self::equalTo($queryModel))
            ->willReturn([
                $serverModelOne,
                $serverModelTwo,
            ]);

        $serverApi->expects(self::at(2))
            ->method('delete')
            ->with(self::equalTo($serverModelOne));

        $serverApi->expects(self::at(3))
            ->method('delete')
            ->with(self::equalTo($serverModelTwo));

        $projectModel = self::createMock(ProjectModelInterface::class);

        $projectModel->expects(self::once())
            ->method('getGuidAsBytes')
            ->willReturn('GuidAsBytesReturn');

        $event = self::createMock(ProjectBeforeDeleteEvent::class);

        $event->expects(self::once())
            ->method('projectModel')
            ->willReturn($projectModel);

        /** @noinspection PhpParamsInspection */
        $listener = new ProjectDeleteListener($serverApi);

        /** @noinspection PhpParamsInspection */
        $listener->call($event);
    }
}
