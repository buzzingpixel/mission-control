<?php

declare(strict_types=1);

namespace src\app\notifications\interfaces;

interface SendNotificationAdapterInterface
{
    /**
     * @return mixed
     */
    public function send(string $subject, string $message, array $context = []);
}
