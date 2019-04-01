<?php

declare(strict_types=1);

namespace src\app\cli\actions;

use function dd;

class DevUtilityAction
{
    public function __invoke() : void
    {
        dd('DevUtilityAction::__invoke');
    }
}
