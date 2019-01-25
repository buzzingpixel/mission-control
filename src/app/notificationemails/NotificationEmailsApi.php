<?php
declare(strict_types=1);

namespace src\app\notificationemails;

use corbomite\di\Di;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\notificationemails\models\NotificationEmailModel;
use src\app\notificationemails\services\SaveNotificationEmailService;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use src\app\notificationemails\interfaces\NotificationEmailModelInterface;
use src\app\notificationemails\exceptions\InvalidNotificationEmailModelException;

class NotificationEmailsApi implements NotificationEmailsApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(): NotificationEmailModelInterface
    {
        return new NotificationEmailModel();
    }

    /**
     * @throws InvalidNotificationEmailModelException
     */
    public function save(NotificationEmailModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveNotificationEmailService::class);
        $service->save($model);
    }

    public function disable(NotificationEmailModelInterface $model): void
    {
        // TODO: Implement disable() method.
    }

    public function enable(NotificationEmailModelInterface $model): void
    {
        // TODO: Implement enable() method.
    }

    public function delete(NotificationEmailModelInterface $model): void
    {
        // TODO: Implement delete() method.
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?NotificationEmailModelInterface {
        $this->limit = 1;
        return $this->fetchAll($params)[0] ?? null;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('email_address', 'asc');
        }

        if ($this->limit) {
            $params->limit($this->limit);
        }

        // TODO: Implement fetchAll() method.
        return [];
    }
}
