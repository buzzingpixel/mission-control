<?php

declare(strict_types=1);

use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\projects\ProjectsApi;
use src\app\projects\services\ArchiveProjectService;
use src\app\projects\services\DeleteProjectService;
use src\app\projects\services\FetchProjectsService;
use src\app\projects\services\SaveProjectService;
use src\app\projects\services\UnArchiveProjectService;
use function DI\autowire;

return [
    ProjectsApi::class => autowire(),
    ProjectsApiInterface::class => autowire(ProjectsApi::class),
    ArchiveProjectService::class => autowire(),
    DeleteProjectService::class => autowire(),
    FetchProjectsService::class => autowire(),
    SaveProjectService::class => autowire(),
    UnArchiveProjectService::class => autowire(),
];
