<?php

declare(strict_types=1);

namespace src\tests\servers\services;

use phpseclib\Net\SSH2;
use PHPUnit\Framework\TestCase;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\services\GetLoggedInServerSshConnection;
use src\app\servers\services\RemoveServerAuthorizedKey;
use Throwable;

class RemoveServerAuthorizedKeyTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $key = 'TestKeyValue';

        $server = self::createMock(ServerModelInterface::class);

        $ssh = self::createMock(SSH2::class);

        $ssh->expects(self::once())
            ->method('exec')
            ->with(
                self::equalTo(
                    'if test -f ~/.ssh/authorized_keys; then ' .
                    'if grep -v "' . $key . '" ~/.ssh/authorized_keys > $HOME/.ssh/tmp; then ' .
                    'cat $HOME/.ssh/tmp > ~/.ssh/authorized_keys && rm $HOME/.ssh/tmp; ' .
                    'fi ' .
                    'fi'
                )
            );

        $getConnection = self::createMock(GetLoggedInServerSshConnection::class);

        $getConnection->expects(self::once())
            ->method('get')
            ->with(self::equalTo($server))
            ->willReturn($ssh);

        /** @noinspection PhpParamsInspection */
        $service = new RemoveServerAuthorizedKey($getConnection);

        /** @noinspection PhpParamsInspection */
        $service($key, $server);
    }
}
