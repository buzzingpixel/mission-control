<?php

declare(strict_types=1);

namespace src\tests\servers\services;

use phpseclib\Net\SSH2;
use PHPUnit\Framework\TestCase;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\services\GetLoggedInServerSshConnection;
use src\app\servers\services\ListServerAuthorizedKeys;
use Throwable;

class ListServerAuthorizedKeysTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $ssh = self::createMock(SSH2::class);

        $ssh->expects(self::once())
            ->method('exec')
            ->with(self::equalTo('cat ~/.ssh/authorized_keys'))
            ->willReturn("\ntestline1 user@thing\ntestline2 more stuff\n");

        $server = self::createMock(ServerModelInterface::class);

        $getConnection = self::createMock(GetLoggedInServerSshConnection::class);

        $getConnection->expects(self::once())
            ->method('get')
            ->with(self::equalTo($server))
            ->willReturn($ssh);

        /** @noinspection PhpParamsInspection */
        $service = new ListServerAuthorizedKeys($getConnection);

        /** @noinspection PhpParamsInspection */
        $returnVal = $service($server);

        self::assertCount(2, $returnVal);

        self::assertEquals('testline1 user@thing', $returnVal[0]);

        self::assertEquals('testline2 more stuff', $returnVal[1]);
    }
}
