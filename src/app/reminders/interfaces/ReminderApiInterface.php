<?php

declare(strict_types=1);

namespace src\app\reminders\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\reminders\exceptions\InvalidReminderModelException;
use src\app\reminders\exceptions\ReminderNameNotUniqueException;

interface ReminderApiInterface
{
    /**
     * Creates a Reminder Model
     */
    public function createModel() : ReminderModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a Reminder Model
     *
     * @return mixed
     *
     * @throws InvalidReminderModelException
     * @throws ReminderNameNotUniqueException
     */
    public function save(ReminderModelInterface $model);

    /**
     * Archives a Reminder Model
     *
     * @return mixed
     */
    public function archive(ReminderModelInterface $model);

    /**
     * Un-archives a Reminder Model
     *
     * @return mixed
     */
    public function unArchive(ReminderModelInterface $model);

    /**
     * Deletes a Reminder Model
     *
     * @return mixed
     */
    public function delete(ReminderModelInterface $model);

    /**
     * Fetches one ping model result based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?ReminderModelInterface;

    /**
     * Fetches all ping models based on params
     *
     * @return ReminderModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;
}
