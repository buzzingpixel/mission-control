<?php
declare(strict_types=1);

namespace src\app\notifications\interfaces;

interface SendNotificationAdapterInterface
{
    public function send(string $sbj, string $message);
}
