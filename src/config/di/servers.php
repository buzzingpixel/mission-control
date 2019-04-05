<?php

declare(strict_types=1);

use src\app\pings\services\DeleteServerService;
use src\app\pings\services\DeleteSSHKeyService;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\ServerApi;
use src\app\servers\services\ArchiveServerService;
use src\app\servers\services\ArchiveSSHKeyService;
use src\app\servers\services\FetchServerService;
use src\app\servers\services\FetchSSHKeyService;
use src\app\servers\services\GenerateSSHKeyService;
use src\app\servers\services\SaveServerService;
use src\app\servers\services\SaveSSHKeyService;
use src\app\servers\services\UnArchiveServerService;
use src\app\servers\services\UnArchiveSSHKeyService;
use src\app\servers\transformers\ServerRecordModelTransformer;
use function DI\autowire;

return [
    ServerApi::class => autowire(),
    ServerApiInterface::class => autowire(ServerApi::class),
    ArchiveServerService::class => autowire(),
    ArchiveSSHKeyService::class => autowire(),
    DeleteServerService::class => autowire(),
    DeleteSSHKeyService::class => autowire(),
    FetchServerService::class => autowire(),
    FetchSSHKeyService::class => autowire(),
    GenerateSSHKeyService::class => autowire(),
    SaveServerService::class => autowire(),
    SaveSSHKeyService::class => autowire(),
    UnArchiveServerService::class => autowire(),
    UnArchiveSSHKeyService::class => autowire(),
    ServerRecordModelTransformer::class => autowire(),
];
