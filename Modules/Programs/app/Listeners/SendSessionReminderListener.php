<?php

namespace Modules\Programs\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Programs\Events\ActivitySessionScheduled;
use Modules\Programs\Jobs\SendSessionReminderMailJob;
use function Symfony\Component\Clock\now;

/**
 * Class SendSessionReminderListener
 * * * Responsibility:
 * Listens for the 'ActivitySessionScheduled' event and schedules a reminder email
 * to be dispatched exactly 24 hours (1 day) before the session starts.
 */
class SendSessionReminderListener
{
    public function __construct() {}

    /**
     * Handle the event.
     * * Logic:
     * Dispatches the 'SendSessionReminderMailJob' with a dynamic delay.
     * The delay is calculated by subtracting one day from the session_date.
     *
     * @param ActivitySessionScheduled $event
     * @return void
     */
    public function handle(ActivitySessionScheduled $event): void
    {
        SendSessionReminderMailJob::dispatch($event->session)
        ->delay($event->session->session_date->subDay());
    }
}
