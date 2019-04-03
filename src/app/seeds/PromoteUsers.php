<?php

declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Phinx\Seed\AbstractSeed;

class PromoteUsers extends AbstractSeed
{
    /**
     * @return string[]
     */
    public function getDependencies() : array
    {
        return ['CreateUsers'];
    }

    public function run() : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $userApi = $di->get(UserApi::class);

        $userNames = explode(',', getenv('SEEDER_PROMOTE_USERS') ?: '');

        if (! $userNames) {
            return;
        }

        foreach ($userNames as $userName) {
            $user = $userApi->fetchUser($userName);

            $user->setExtendedProperty('is_admin', 1);

            /** @noinspection PhpUnhandledExceptionInspection */
            $userApi->saveUser($user);
        }
    }
}
