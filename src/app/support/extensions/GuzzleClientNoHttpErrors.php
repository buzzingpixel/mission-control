<?php
declare(strict_types=1);

namespace src\app\support\extensions;

use GuzzleHttp\Client;

class GuzzleClientNoHttpErrors extends Client
{
    public function __construct()
    {
        parent::__construct([
            'http_errors' => false,
        ]);
    }
}
