<?php

declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\exceptions\UserExistsException;
use corbomite\user\UserApi;
use Phinx\Seed\AbstractSeed;

class CreateUsers extends AbstractSeed
{
    public function run() : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $userApi = $di->get(UserApi::class);

        $userNames = explode(',', getenv('SEEDER_USER_NAMES') ?: '');
        $passwords = explode(',', getenv('SEEDER_USER_PASSWORDS') ?: '');

        if (! count($userNames) ||
            ! count($passwords)
        ) {
            throw new LogicException(
                'SEEDER_USER_NAMES and SEEDER_USER_PASSWORDS not set appropriately'
            );
        }

        foreach (array_keys($userNames) as $key) {
            if (! $userNames[$key] || ! $passwords[$key]) {
                throw new LogicException(
                    'SEEDER_USER_NAMES and SEEDER_USER_PASSWORDS not set appropriately'
                );
            }
        }

        foreach (array_keys($userNames) as $key) {
            try {
                /** @noinspection PhpUnhandledExceptionInspection */
                $userApi->registerUser($userNames[$key], $passwords[$key]);
            } catch (UserExistsException $e) {
            }
        }
    }
}
