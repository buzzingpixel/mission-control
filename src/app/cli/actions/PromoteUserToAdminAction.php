<?php
declare(strict_types=1);

namespace src\app\cli\actions;

use corbomite\cli\services\CliQuestionService;
use corbomite\user\interfaces\UserApiInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PromoteUserToAdminAction
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

        $user = $this->userApi->fetchUser($emailAddress);

        $user->userDataItem('admin', true);

        $this->userApi->saveUser($user);

        $this->consoleOutput->writeln(
            '<fg=green>User ' . $emailAddress . ' promoted to admin!</>'
        );
    }
}
