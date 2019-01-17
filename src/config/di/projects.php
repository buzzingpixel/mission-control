<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidFactory;
use src\app\projects\ProjectsApi;
use src\app\datasupport\BuildQuery;
use corbomite\db\Factory as OrmFactory;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\services\SaveProjectService;

return [
    ProjectsApi::class => function () {
        return new ProjectsApi(new Di());
    },
    SaveProjectService::class => function () {
        return new SaveProjectService(
            new Slugify(),
            new OrmFactory(),
            new UuidFactory(),
            Di::get(BuildQuery::class),
            new FetchDataParamsFactory()
        );
    },
];
