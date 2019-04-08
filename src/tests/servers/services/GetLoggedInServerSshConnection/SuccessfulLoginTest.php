<?php

declare(strict_types=1);

namespace src\tests\servers\services\GetLoggedInServerSshConnection;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use PHPUnit\Framework\TestCase;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\services\GetLoggedInServerSshConnection;
use src\app\utilities\RSAFactory;
use src\app\utilities\SSH2Factory;
use Throwable;

class SuccessfulLoginTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $rsa = self::createMock(RSA::class);

        $sshKeyModel = self::createMock(SSHKeyModelInterface::class);

        $sshKeyModel->expects(self::once())
            ->method('private')
            ->willReturn('SshKeyModelPrivateTestValue');

        $sshKeyModel->expects(self::once())
            ->method('public')
            ->willReturn('SshKeyModelPublicTestValue');

        $ssh = self::createMock(SSH2::class);

        $ssh->expects(self::once())
            ->method('login')
            ->with(
                self::equalTo('ServerSshUserNameTestValue'),
                self::equalTo($rsa)
            )
            ->willReturn(true);

        $ssh2Factory = self::createMock(SSH2Factory::class);

        $ssh2Factory->expects(self::once())
            ->method('make')
            ->with(
                self::equalTo('ServerAddressTestValue'),
                self::equalTo(123)
            )
            ->willReturn($ssh);

        $rsaFactory = self::createMock(RSAFactory::class);

        $rsaFactory->expects(self::once())
            ->method('make')
            ->with(
                self::equalTo('SshKeyModelPrivateTestValue'),
                self::equalTo('SshKeyModelPublicTestValue')
            )
            ->willReturn($rsa);

        $server = self::createMock(ServerModelInterface::class);

        $server->expects(self::once())
            ->method('address')
            ->willReturn('ServerAddressTestValue');

        $server->expects(self::once())
            ->method('sshPort')
            ->willReturn(123);

        $server->expects(self::once())
            ->method('sshKeyModel')
            ->willReturn($sshKeyModel);

        $server->expects(self::once())
            ->method('sshUserName')
            ->willReturn('ServerSshUserNameTestValue');

        /** @noinspection PhpParamsInspection */
        $service = new GetLoggedInServerSshConnection($ssh2Factory, $rsaFactory);

        /** @noinspection PhpParamsInspection */
        self::assertSame($ssh, $service($server));
    }
}
