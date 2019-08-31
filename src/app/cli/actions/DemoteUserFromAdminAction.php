<?php

declare(strict_types=1);

namespace src\app\cli\actions;

use corbomite\cli\services\CliQuestionService;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Symfony\Component\Console\Output\OutputInterface;

class DemoteUserFromAdminAction
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var OutputInterface */
    private $consoleOutput;
    /** @var CliQuestionService */
    private $cliQuestionService;

    public function __construct(
        UserApiInterface $userApi,
        OutputInterface $consoleOutput,
        CliQuestionService $cliQuestionService
    ) {
        $this->userApi            = $userApi;
        $this->consoleOutput      = $consoleOutput;
        $this->cliQuestionService = $cliQuestionService;
    }

    public function __invoke() : void
    {
        $emailAddress = $this->cliQuestionService->ask(
            '<fg=cyan>Email address: </>'
        );

        $user = $this->userApi->fetchUser($emailAddress);

        if (! $user) {
            throw new LogicException('User not found');
        }

        $user->setExtendedProperty('is_admin', 0);

        $this->userApi->saveUser($user);

        $this->consoleOutput->writeln(
            '<fg=green>User ' . $emailAddress . ' demoted from admin.</>'
        );
    }
}
