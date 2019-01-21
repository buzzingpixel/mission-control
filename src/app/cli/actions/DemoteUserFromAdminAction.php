<?php
declare(strict_types=1);

namespace src\app\cli\actions;

use LogicException;
use corbomite\cli\services\CliQuestionService;
use corbomite\user\interfaces\UserApiInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoteUserFromAdminAction
{
    private $userApi;
    private $consoleOutput;
    private $cliQuestionService;

    public function __construct(
        UserApiInterface $userApi,
        OutputInterface $consoleOutput,
        CliQuestionService $cliQuestionService
    ) {
        $this->userApi = $userApi;
        $this->consoleOutput = $consoleOutput;
        $this->cliQuestionService = $cliQuestionService;
    }

    public function __invoke()
    {
        $emailAddress = $this->cliQuestionService->ask(
            '<fg=cyan>Email address: </>'
        );

        if (! $user = $this->userApi->fetchUser($emailAddress)) {
            throw new LogicException('User not found');
        }

        $user->setExtendedProperty('is_admin', 0);

        $this->userApi->saveUser($user);

        $this->consoleOutput->writeln(
            '<fg=green>User ' . $emailAddress . ' promoted to admin!</>'
        );
    }
}
