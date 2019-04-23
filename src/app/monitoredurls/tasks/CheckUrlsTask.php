<?php

declare(strict_types=1);

namespace src\app\monitoredurls\tasks;

use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class CheckUrlsTask
{
    public const BATCH_NAME  = 'checkUrls';
    public const BATCH_TITLE = 'Check URLS';

    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;
    /** @var CheckUrl */
    private $checkUrl;

    public function __construct(
        MonitoredUrlsApiInterface $monitoredUrlsApi,
        CheckUrl $checkUrl
    ) {
        $this->monitoredUrlsApi = $monitoredUrlsApi;
        $this->checkUrl         = $checkUrl;
    }

    public function __invoke() : void
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->checkUrl->checkUrl($model);
        }
    }
}
