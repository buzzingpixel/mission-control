<?php
declare(strict_types=1);

namespace src\app\notificationemails\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\notificationemails\exceptions\InvalidNotificationEmailModelException;

interface NotificationEmailsApiInterface
{
    /**
     * Creates a Reminder Model
     * @return NotificationEmailModelInterface
     */
    public function createModel(): NotificationEmailModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     * @param string $string
     * @return string
     */
    public function uuidToBytes(string $string): string;

    /**
     * Creates a Fetch Data Params instance
     * @return QueryModelInterface
     */
    public function makeQueryModel(): QueryModelInterface;

    /**
     * Saves a Notification Email Model
     * @param NotificationEmailModelInterface $model
     * @throws InvalidNotificationEmailModelException
     */
    public function save(NotificationEmailModelInterface $model);

    /**
     * Disables a Notification Email Model
     * @param NotificationEmailModelInterface $model
     */
    public function disable(NotificationEmailModelInterface $model);

    /**
     * Enables a Notification Email Model
     * @param NotificationEmailModelInterface $model
     */
    public function enable(NotificationEmailModelInterface $model);

    /**
     * Deletes a Notification Email Model
     * @param NotificationEmailModelInterface $model
     */
    public function delete(NotificationEmailModelInterface $model);

    /**
     * Fetches one Notification Email model result based on params
     * @param QueryModelInterface $params
     * @return NotificationEmailModelInterface|null
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?NotificationEmailModelInterface;

    /**
     * Fetches all Notification Email models based on params
     * @param QueryModelInterface $params
     * @return NotificationEmailModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null): array;
}
