<?php
declare(strict_types=1);

namespace src\app\cli\actions;

class DevUtilityAction
{
    public function __invoke()
    {
        var_dump('DevUtilityAction::__invoke');
    }
}
