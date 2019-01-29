<?php
declare(strict_types=1);

namespace src\app\notifications\interfaces;

interface SendNotificationAdapterInterface
{
    public function send(string $subject, string $message, array $context = []);
}
