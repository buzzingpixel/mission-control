<?php

declare(strict_types=1);

namespace src\app\tickets\listeners;

use buzzingpixel\corbomitemailer\EmailApi;
use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use corbomite\user\interfaces\UserApiInterface;
use src\app\tickets\events\TicketAfterSaveEvent;
use Throwable;
use function getenv;

class TicketSaveListener implements EventListenerInterface
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var EmailApi */
    private $emailApi;

    public function __construct(
        UserApiInterface $userApi,
        EmailApi $emailApi
    ) {
        $this->userApi  = $userApi;
        $this->emailApi = $emailApi;
    }

    /**
     * @throws Throwable
     */
    public function call(EventInterface $event) : void
    {
        /** @noinspection PhpParamsInspection */
        $this->respond($event);
    }

    /**
     * @throws Throwable
     */
    private function respond(TicketAfterSaveEvent $event) : void
    {
        $ticket = $event->model();

        $emailAddresses = [
            $ticket->createdByUser()->emailAddress() => $ticket->createdByUser()->emailAddress(),
        ];

        if ($ticket->assignedToUser()) {
            $emailAddresses[$ticket->assignedToUser()->emailAddress()] = $ticket->assignedToUser()->emailAddress();
        }

        foreach ($ticket->watchers() as $watcher) {
            $emailAddresses[$watcher->emailAddress()] = $watcher->emailAddress();
        }

        $currentUser = $this->userApi->fetchCurrentUser();

        if ($currentUser) {
            unset($emailAddresses[$currentUser->emailAddress()]);
        }

        $subject = 'Ticket "' . $ticket->title() . '" ';

        if ($event->new()) {
            $subject .= 'has been created';
        } elseif (! $event->new()) {
            $subject .= 'has been updated';
        }

        $link = getenv('SITE_URL') . '/tickets/ticket/' . $ticket->guid();

        foreach ($emailAddresses as $emailAddress) {
            if ($currentUser->emailAddress() === $emailAddress) {
                continue;
            }

            $this->emailApi->addEmailToQueue($this->emailApi->createEmailModel([
                'toEmail' => $emailAddress,
                'subject' => $subject,
                'messagePlainText' => $link,
            ]));
        }
    }
}
