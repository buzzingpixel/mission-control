<?php
declare(strict_types=1);

namespace src\app\notificationemails;

use Psr\Container\ContainerInterface;
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

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createModel(): NotificationEmailModelInterface
    {
        return new NotificationEmailModel();
    }

    public function save(NotificationEmailModelInterface $model): void
    {
        $service = $this->di->get(SaveNotificationEmailService::class);
        $service->save($model);
    }

    public function disable(NotificationEmailModelInterface $model): void
    {
        $service = $this->di->get(DisableNotificationEmailService::class);
        $service->disable($model);
    }

    public function enable(NotificationEmailModelInterface $model): void
    {
        $service = $this->di->get(EnableNotificationEmailService::class);
        $service->enable($model);
    }

    public function delete(NotificationEmailModelInterface $model): void
    {
        $service = $this->di->get(DeleteNotificationEmailService::class);
        $service->delete($model);
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?NotificationEmailModelInterface {
        $this->limit = 1;
        $result = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;
        return $result;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        $service = $this->di->get(FetchNotificationEmailService::class);

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
