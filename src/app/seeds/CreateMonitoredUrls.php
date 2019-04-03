<?php

declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\projects\ProjectsApi;

class CreateMonitoredUrls extends AbstractSeed
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
        $this->createMonitoredUrl('BuzzingPixel', 'https://buzzingpixel.com');
        $this->createMonitoredUrl('NightOwl', 'https://nightowl.fm');
        $this->createMonitoredUrl('DuBose Web', 'https://www.duboseweb.com');
    }

    private function createMonitoredUrl(string $title, string $url) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $projectApi = $di->get(ProjectsApi::class);

        $projectQuery = $projectApi->makeQueryModel();

        $projectQuery->addWhere('title', 'Test Project 1');

        $project = $projectApi->fetchOne($projectQuery);

        /** @noinspection PhpUnhandledExceptionInspection */
        $monitoredUrlApi = $di->get(MonitoredUrlsApi::class);

        $model = $monitoredUrlApi->createModel();

        $model->title($title);

        $model->url($url);

        $model->projectGuid($project->guid());

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $monitoredUrlApi->save($model);
        } catch (Throwable $e) {
        }
    }
}
