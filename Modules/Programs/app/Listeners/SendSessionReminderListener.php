<?php

namespace Modules\Programs\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Programs\Events\ActivitySessionScheduled;
use Modules\Programs\Jobs\SendSessionReminderMailJob;

use function Symfony\Component\Clock\now;

class SendSessionReminderListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ActivitySessionScheduled $event): void
    {
        SendSessionReminderMailJob::dispatch($event->session)
        ->delay($event->session->session_date->subDay());
    }
}
