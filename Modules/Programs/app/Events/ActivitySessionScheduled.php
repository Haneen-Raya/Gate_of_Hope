<?php

namespace Modules\Programs\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Programs\Models\ActivitySession;

/**
 * Class ActivitySessionScheduled
 * * This event is dispatched whenever a new Activity Session is successfully scheduled.
 * It serves as a hook for asynchronous listeners such as:
 * 1. Sending reminder emails to trainers/attendees.
 * 2. Logging specific program milestones.
 * 3. Triggering real-time dashboard notifications.
 */
class ActivitySessionScheduled
{
    use Dispatchable, SerializesModels;

    /**
     * The session instance that was scheduled.
     * @var ActivitySession
     */
    public $session;

    /**
     * Create a new event instance.
     * @param ActivitySession $session
     */
    public function __construct(ActivitySession $session)
    {
        $this->session = $session;
    }
}
