<?php

declare(strict_types=1);

use buzzingpixel\corbomitemailer\EmailApi;
use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\services\BuildQueryService;
use corbomite\flashdata\FlashDataApi;
use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\http\RequestHelper;
use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\queue\QueueApi;
use corbomite\requestdatastore\DataStore;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\user\UserApi;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Diactoros\ResponseFactory;
use function DI\autowire;

return [
    BuildQueryInterface::class => autowire(BuildQueryService::class),
    DataStoreInterface::class => autowire(DataStore::class),
    EmailApiInterface::class => autowire(EmailApi::class),
    FlashDataApiInterface::class => autowire(FlashDataApi::class),
    OutputInterface::class => autowire(ConsoleOutput::class),
    QueueApiInterface::class => autowire(QueueApi::class),
    RequestHelperInterface::class => static function (ContainerInterface $di) {
        return $di->get(RequestHelper::class);
    },
    ResponseInterface::class => static function () {
        return (new ResponseFactory())->createResponse();
    },
    UserApiInterface::class => autowire(UserApi::class),
];
