<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use corbomite\cli\services\CliQuestionService;
use src\app\cli\actions\PromoteUserToAdminAction;
use Symfony\Component\Console\Output\ConsoleOutput;

return [
    PromoteUserToAdminAction::class => function () {
        return new PromoteUserToAdminAction(
            Di::get(UserApi::class),
            new ConsoleOutput(),
            Di::get(CliQuestionService::class)
        );
    },
];
