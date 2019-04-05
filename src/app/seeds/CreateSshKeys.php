<?php

declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\servers\ServerApi;

class CreateSshKeys extends AbstractSeed
{
    /**
     * @return string[]
     */
    public function getDependencies() : array
    {
        return ['CreateProjects'];
    }

    public function run() : void
    {
        $this->createSshKey('Test Key 1');
        $this->createSshKey('Test Key 2');
        $this->createSshKey('Test Key 3');
    }

    private function createSshKey(string $title) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $serverApi = $di->get(ServerApi::class);

        $model = $serverApi->createSShKeyModel();

        $model->title($title);

        $key = $serverApi->generateSSHKey();

        $model->public($key['publickey']);

        $model->private($key['privatekey']);

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            $serverApi->saveSSHKey($model);
        } catch (Throwable $e) {
        }
    }
}
