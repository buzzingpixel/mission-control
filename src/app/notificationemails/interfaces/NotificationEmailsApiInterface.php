<?php

declare(strict_types=1);

namespace src\app\notificationemails\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\notificationemails\exceptions\InvalidNotificationEmailModelException;
use src\app\notificationemails\exceptions\NotificationEmailNotUniqueException;

interface NotificationEmailsApiInterface
{
    /**
     * Creates a Reminder Model
     */
    public function createModel() : NotificationEmailModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a Notification Email Model
     *
     * @throws InvalidNotificationEmailModelException
     * @throws NotificationEmailNotUniqueException
     */
    public function save(NotificationEmailModelInterface $model) : void;

    /**
     * Disables a Notification Email Model
     */
    public function disable(NotificationEmailModelInterface $model) : void;

    /**
     * Enables a Notification Email Model
     */
    public function enable(NotificationEmailModelInterface $model) : void;

    /**
     * Deletes a Notification Email Model
     */
    public function delete(NotificationEmailModelInterface $model) : void;

    /**
     * Fetches one Notification Email model result based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?NotificationEmailModelInterface;

    /**
     * Fetches all Notification Email models based on params
     *
     * @return NotificationEmailModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;
}
