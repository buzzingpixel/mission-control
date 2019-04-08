<?php

declare(strict_types=1);

namespace src\tests\servers\services\ListServerAuthorizedKeysTest;

use LogicException;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use PHPUnit\Framework\TestCase;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\services\ListServerAuthorizedKeys;
use src\app\utilities\RSAFactory;
use src\app\utilities\SSH2Factory;
use Throwable;

class FailedLoginTest extends TestCase
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
            ->willReturn(false);

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
        $service = new ListServerAuthorizedKeys($ssh2Factory, $rsaFactory);

        $exception = null;

        try {
            /** @noinspection PhpParamsInspection */
            $service($server);
        } catch (LogicException $e) {
            $exception = $e;
        }

        self::assertInstanceOf(LogicException::class, $exception);

        self::assertEquals('Unable to log in to SSH Server', $exception->getMessage());
    }
}
