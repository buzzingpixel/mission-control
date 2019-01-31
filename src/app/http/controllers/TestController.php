<?php
declare(strict_types=1);

namespace src\app\http\controllers;

class TestController
{
    public function __invoke()
    {
        dd('here');
    }
}
