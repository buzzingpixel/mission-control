<?php
declare(strict_types=1);

use src\app\projects\ProjectsApi;
use src\app\monitoredurls\listeners\ProjectDeleteListener;
use src\app\monitoredurls\listeners\ProjectArchiveListener;
use src\app\monitoredurls\listeners\ProjectUnArchiveListener;
use corbomite\events\interfaces\EventListenerRegistrationInterface;

/** @var EventListenerRegistrationInterface $r */

// Monitored URL Project Listeners
$r->register(ProjectsApi::class, 'ProjectBeforeArchive', ProjectArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeUnArchive', ProjectUnArchiveListener::class);
$r->register(ProjectsApi::class, 'ProjectBeforeDelete', ProjectDeleteListener::class);
