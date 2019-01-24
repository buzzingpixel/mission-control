<?php
declare(strict_types=1);

namespace src\app\reminders;

use corbomite\di\Di;
use src\app\reminders\models\ReminderModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\reminders\services\SaveReminderService;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\reminders\exceptions\InvalidReminderModelException;
use src\app\reminders\exceptions\ReminderNameNotUniqueException;

class ReminderApi implements ReminderApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(): ReminderModelInterface
    {
        return new ReminderModel();
    }

    /**
     * @throws InvalidReminderModelException
     * @throws ReminderNameNotUniqueException
     */
    public function save(ReminderModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveReminderService::class);
        $service->save($model);
    }

    public function archive(ReminderModelInterface $model): void
    {
        // TODO: Implement archive() method.
    }

    public function unArchive(ReminderModelInterface $model): void
    {
        // TODO: Implement unArchive() method.
    }

    public function delete(ReminderModelInterface $model): void
    {
        // TODO: Implement delete() method.
    }

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ReminderModelInterface {
        return $this->fetchAll($params)[0] ?? null;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        // TODO: Implement fetchAll() method.
        return [];
    }
}
