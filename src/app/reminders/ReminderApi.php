<?php
declare(strict_types=1);

namespace src\app\reminders;

use corbomite\di\Di;
use src\app\reminders\models\ReminderModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\reminders\services\SaveReminderService;
use src\app\reminders\services\FetchReminderService;
use src\app\reminders\services\DeleteReminderService;
use src\app\reminders\services\ArchiveReminderService;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\services\UnArchiveReminderService;
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
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchiveReminderService::class);
        $service->archive($model);
    }

    public function unArchive(ReminderModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(UnArchiveReminderService::class);
        $service->unArchive($model);
    }

    public function delete(ReminderModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeleteReminderService::class);
        $service->delete($model);
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ReminderModelInterface {
        $this->limit = 1;
        $result = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;
        return $result;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchReminderService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        if ($this->limit) {
            $params->limit($this->limit);
        }

        return $service->fetch($params);
    }
}
