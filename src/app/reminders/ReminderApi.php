<?php

declare(strict_types=1);

namespace src\app\reminders;

use corbomite\db\interfaces\QueryModelInterface;
use Psr\Container\ContainerInterface;
use src\app\reminders\exceptions\InvalidReminderModelException;
use src\app\reminders\exceptions\ReminderNameNotUniqueException;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\reminders\models\ReminderModel;
use src\app\reminders\services\ArchiveReminderService;
use src\app\reminders\services\DeleteReminderService;
use src\app\reminders\services\FetchReminderService;
use src\app\reminders\services\SaveReminderService;
use src\app\reminders\services\UnArchiveReminderService;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\support\traits\UuidToBytesTrait;

class ReminderApi implements ReminderApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createModel() : ReminderModelInterface
    {
        return new ReminderModel();
    }

    /**
     * @throws InvalidReminderModelException
     * @throws ReminderNameNotUniqueException
     */
    public function save(ReminderModelInterface $model) : void
    {
        $service = $this->di->get(SaveReminderService::class);
        $service->save($model);
    }

    public function archive(ReminderModelInterface $model) : void
    {
        $service = $this->di->get(ArchiveReminderService::class);
        $service->archive($model);
    }

    public function unArchive(ReminderModelInterface $model) : void
    {
        $service = $this->di->get(UnArchiveReminderService::class);
        $service->unArchive($model);
    }

    public function delete(ReminderModelInterface $model) : void
    {
        $service = $this->di->get(DeleteReminderService::class);
        $service->delete($model);
    }

    /** @var ?int */
    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?ReminderModelInterface {
        $this->limit = 1;
        $result      = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;

        return $result;
    }

    /**
     * @return ReminderModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array
    {
        $service = $this->di->get(FetchReminderService::class);

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
