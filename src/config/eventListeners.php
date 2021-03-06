<?php

declare(strict_types=1);

use corbomite\events\interfaces\EventListenerRegistrationInterface;
use src\app\monitoredurls\listeners\MonitoredUrlDeleteListener;
use src\app\monitoredurls\listeners\ProjectArchiveListener as MonitoredUrlsProjectArchiveListener;
use src\app\monitoredurls\listeners\ProjectDeleteListener as MonitoredUrlsProjectDeleteListener;
use src\app\monitoredurls\listeners\ProjectUnArchiveListener as MonitoredUrlsProjectUnArchiveListener;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\pings\listeners\ProjectArchiveListener as PingProjectArchiveListener;
use src\app\pings\listeners\ProjectDeleteListener as PingProjectDeleteListener;
use src\app\pings\listeners\ProjectUnArchiveListener as PingProjectUnArchiveListener;
use src\app\pipelines\listeners\ProjectArchiveListener as PipelinesProjectArchiveListener;
use src\app\pipelines\listeners\ProjectDeleteListener as PipelinesProjectDeleteListener;
use src\app\pipelines\listeners\ProjectUnArchiveListener as PipelinesProjectUnArchiveListener;
use src\app\pipelines\listeners\SavePipelineJobListener;
use src\app\pipelines\PipelineApi;
use src\app\projects\ProjectsApi;
use src\app\reminders\listeners\ProjectArchiveListener as ReminderProjectArchiveListener;
use src\app\reminders\listeners\ProjectDeleteListener as ReminderProjectDeleteListener;
use src\app\reminders\listeners\ProjectUnArchiveListener as ReminderProjectUnArchiveListener;
use src\app\servers\listeners\ProjectArchiveListener as ServerProjectArchiveListener;
use src\app\servers\listeners\ProjectDeleteListener as ServerProjectDeleteListener;
use src\app\servers\listeners\ProjectUnArchiveListener as ServerProjectUnArchiveListener;
use src\app\tickets\listeners\TicketSaveListener;
use src\app\tickets\listeners\TicketThreadItemSaveListener;
use src\app\tickets\TicketApi;

/** @var EventListenerRegistrationInterface $r */

// Monitored URL delete listeners
$r->register(MonitoredUrlsApi::class, 'MonitoredUrlBeforeDelete', MonitoredUrlDeleteListener::class);

// Monitored URL Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', MonitoredUrlsProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', MonitoredUrlsProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', MonitoredUrlsProjectUnArchiveListener::class);

// Ping Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', PingProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', PingProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', PingProjectUnArchiveListener::class);

// Reminder Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', ReminderProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', ReminderProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', ReminderProjectUnArchiveListener::class);

// Pipeline Listeners
$r->register(PipelineApi::class, 'PipelineJobAfterSave', SavePipelineJobListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', PipelinesProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', PipelinesProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', PipelinesProjectUnArchiveListener::class);

// Server listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', ServerProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', ServerProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', ServerProjectUnArchiveListener::class);

// Ticket listeners
$r->register(TicketApi::class, 'TicketAfterSave', TicketSaveListener::class);
$r->register(TicketApi::class, 'TicketThreadItemAfterSave', TicketThreadItemSaveListener::class);
