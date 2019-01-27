<?php
declare(strict_types=1);

namespace src\app\notificationemails;

use corbomite\di\Di;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\notificationemails\models\NotificationEmailModel;
use src\app\notificationemails\services\SaveNotificationEmailService;
use src\app\notificationemails\services\FetchNotificationEmailService;
use src\app\notificationemails\services\DeleteNotificationEmailService;
use src\app\notificationemails\services\EnableNotificationEmailService;
use src\app\notificationemails\services\DisableNotificationEmailService;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use src\app\notificationemails\interfaces\NotificationEmailModelInterface;
use src\app\notificationemails\exceptions\NotificationEmailNotUniqueException;
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
     * @throws NotificationEmailNotUniqueException
     */
    public function save(NotificationEmailModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveNotificationEmailService::class);
        $service->save($model);
    }

    public function disable(NotificationEmailModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DisableNotificationEmailService::class);
        $service->disable($model);
    }

    public function enable(NotificationEmailModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(EnableNotificationEmailService::class);
        $service->enable($model);
    }

    public function delete(NotificationEmailModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeleteNotificationEmailService::class);
        $service->delete($model);
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
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchNotificationEmailService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('email_address', 'asc');
        }

        if ($this->limit) {
            $params->limit($this->limit);
        }

        return $service->fetch($params);
    }
}
