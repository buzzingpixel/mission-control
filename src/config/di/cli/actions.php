<?php
declare(strict_types=1);

use corbomite\user\UserApi;
use Psr\Container\ContainerInterface;
use corbomite\cli\services\CliQuestionService;
use src\app\cli\actions\PromoteUserToAdminAction;
use src\app\cli\actions\DemoteUserFromAdminAction;
use Symfony\Component\Console\Output\ConsoleOutput;

return [
    PromoteUserToAdminAction::class => static function (ContainerInterface $di) {
        return new PromoteUserToAdminAction(
            $di->get(UserApi::class),
            new ConsoleOutput(),
            $di->get(CliQuestionService::class)
        );
    },
    DemoteUserFromAdminAction::class => static function (ContainerInterface $di) {
        return new DemoteUserFromAdminAction(
            $di->get(UserApi::class),
            new ConsoleOutput(),
            $di->get(CliQuestionService::class)
        );
    },
];
