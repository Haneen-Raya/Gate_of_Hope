<?php

namespace Modules\Programs\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Programs\Models\ActivitySession;

/**
 * Class SessionReminderMail
 * * Orchestrates the automated reminder emails for upcoming activity sessions.
 * Implements ShouldQueue to delegate email rendering and transmission to a
 * background worker, ensuring zero latency for the trigger process.
 * * @package Modules\Programs\Emails
 */
class SessionReminderMail extends Mailable implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new message instance using PHP 8.x Constructor Promotion.
     * * @param ActivitySession $session The specific session instance containing
     * schedule and location details.
     */
    public function __construct(
        public ActivitySession $session
    ) {}

    /**
     * Construct and configure the email message.
     * * This method defines the email template, subject line, and the data
     * payload required for the view.
     * * @return self
     */
    public function build(): self
    {
        return $this->subject(__('emails.session_reminder_subject'))
            ->view('emails.session-reminder')
            ->with([
                'sessionDate' => $this->session->session_date,
                'startTime'   => $this->session->start_time,
                'programName' => $this->session->activity->program->name ?? 'N/A',
            ]);
    }
}
