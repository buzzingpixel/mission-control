<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use function dd;

class TestController
{
    public function __invoke() : void
    {
        dd('here');
    }
}
