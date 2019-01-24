<?php
declare(strict_types=1);

use src\app\projects\ProjectsApi;
use src\app\monitoredurls\listeners\ProjectDeleteListener;
use src\app\monitoredurls\listeners\ProjectArchiveListener;
use src\app\monitoredurls\listeners\ProjectUnArchiveListener;
use corbomite\events\interfaces\EventListenerRegistrationInterface;
use src\app\pings\listeners\ProjectDeleteListener as PingProjectDeleteListener;
use src\app\pings\listeners\ProjectArchiveListener as PingProjectArchiveListener;
use src\app\pings\listeners\ProjectUnArchiveListener as PingProjectUnArchiveListener;
use src\app\reminders\listeners\ProjectDeleteListener as ReminderProjectDeleteListener;
use src\app\reminders\listeners\ProjectArchiveListener as ReminderProjectArchiveListener;
use src\app\reminders\listeners\ProjectUnArchiveListener as ReminderProjectUnArchiveListener;

/** @var EventListenerRegistrationInterface $r */

// Monitored URL Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', ProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', ProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', ProjectUnArchiveListener::class);

// Ping Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', PingProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', PingProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', PingProjectUnArchiveListener::class);

// Reminder Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', ReminderProjectDeleteListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', ReminderProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', ReminderProjectUnArchiveListener::class);
