<?php

declare(strict_types=1);

use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use corbomite\events\EventDispatcher;
use phpseclib\Crypt\RSA;
use Psr\Container\ContainerInterface;
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

return [
    ServerApi::class => static function (ContainerInterface $di) {
        return new ServerApi($di);
    },
    ServerApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(ServerApi::class);
    },
    ArchiveServerService::class => static function (ContainerInterface $di) {
        return new ArchiveServerService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    ArchiveSSHKeyService::class => static function (ContainerInterface $di) {
        return new ArchiveSSHKeyService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeleteServerService::class => static function (ContainerInterface $di) {
        return new DeleteServerService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeleteSSHKeyService::class => static function (ContainerInterface $di) {
        return new DeleteSSHKeyService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    FetchServerService::class => static function (ContainerInterface $di) {
        return new FetchServerService(
            $di->get(BuildQueryService::class),
            $di->get(ServerRecordModelTransformer::class)
        );
    },
    FetchSSHKeyService::class => static function (ContainerInterface $di) {
        return new FetchSSHKeyService(
            $di->get(BuildQueryService::class)
        );
    },
    GenerateSSHKeyService::class => static function () {
        return new GenerateSSHKeyService(new RSA());
    },
    SaveServerService::class => static function (ContainerInterface $di) {
        return new SaveServerService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    SaveSSHKeyService::class => static function (ContainerInterface $di) {
        return new SaveSSHKeyService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchiveServerService::class => static function (ContainerInterface $di) {
        return new UnArchiveServerService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchiveSSHKeyService::class => static function (ContainerInterface $di) {
        return new UnArchiveSSHKeyService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    ServerRecordModelTransformer::class => static function (ContainerInterface $di) {
        return new ServerRecordModelTransformer($di);
    },
];
