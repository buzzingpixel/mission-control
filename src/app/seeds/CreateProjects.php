<?php
declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\projects\ProjectsApi;

class CreateProjects extends AbstractSeed
{
    public function run()
    {
        $desc = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla pharetra porttitor felis vitae molestie. Aliquam erat volutpat. Vestibulum ut euismod mauris.';

        $this->createProject('Test Project 1', $desc);
        $this->createProject('Test Project 2', $desc);
        $this->createProject('Test Project 3', $desc);
    }

    private function createProject(string $name, string $desc)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $projectApi = $di->get(ProjectsApi::class);

        $model = $projectApi->createModel();

        $model->title($name);

        $model->description($desc);

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            $projectApi->save($model);
        } catch (\Throwable $e) {
        }
    }
}
