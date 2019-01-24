<?php
declare(strict_types=1);

namespace src\app\monitoredurls\listeners;

use corbomite\db\PDO;
use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\monitoredurls\events\MonitoredUrlBeforeDeleteEvent;

class MonitoredUrlDeleteListener implements EventListenerInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function call(EventInterface $event): void
    {
        /** @var MonitoredUrlBeforeDeleteEvent $event */

        $sql = 'DELETE FROM monitored_url_incidents WHERE monitored_url_guid = ?';

        $q = $this->pdo->prepare($sql);

        $q->execute([$event->monitoredUrlModel()->getGuidAsBytes()]);
    }
}
