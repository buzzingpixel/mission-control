<?php

declare(strict_types=1);

use buzzingpixel\corbomitemailer\EmailApi;
use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use corbomite\flashdata\FlashDataApi;
use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\http\RequestHelper;
use corbomite\requestdatastore\DataStore;
use corbomite\requestdatastore\DataStoreInterface;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\user\UserApi;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Diactoros\ResponseFactory;

return [
    DataStoreInterface::class => static function (ContainerInterface $di) {
        return $di->get(DataStore::class);
    },
    EmailApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(EmailApi::class);
    },
    FlashDataApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(FlashDataApi::class);
    },
    OutputInterface::class => static function () {
        return new ConsoleOutput();
    },
    RequestHelperInterface::class => static function (ContainerInterface $di) {
        return $di->get(RequestHelper::class);
    },
    ResponseInterface::class => static function () {
        return (new ResponseFactory())->createResponse();
    },
    UserApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(UserApi::class);
    },
];
