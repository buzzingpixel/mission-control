<?php

declare(strict_types=1);

namespace src\tests\servers\services\AddServerAuthorizedKey;

use phpseclib\Net\SSH2;
use PHPUnit\Framework\TestCase;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\services\AddServerAuthorizedKey;
use src\app\servers\services\GetLoggedInServerSshConnection;
use Throwable;

class KeyDoesNotExistTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $key = 'TestKeyValue';

        $ssh = self::createMock(SSH2::class);

        $ssh->expects(self::at(0))
            ->method('exec')
            ->with(self::equalTo('grep "' . $key . '" ~/.ssh/authorized_keys;'))
            ->willReturn('');

        $ssh->expects(self::at(1))
            ->method('exec')
            ->with(self::equalTo('echo "' . $key . '" >> ~/.ssh/authorized_keys;'))
            ->willReturn('');

        $server = self::createMock(ServerModelInterface::class);

        $getConnection = self::createMock(GetLoggedInServerSshConnection::class);

        $getConnection->expects(self::once())
            ->method('get')
            ->with(self::equalTo($server))
            ->willReturn($ssh);

        /** @noinspection PhpParamsInspection */
        $service = new AddServerAuthorizedKey($getConnection);

        /** @noinspection PhpParamsInspection */
        $service($key, $server);
    }
}
