<?php

namespace Modules\Programs\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Programs\Models\ActivitySession;

class SessionReminderMail extends Mailable implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param ActivitySession $session
     */
    public function __construct(
        public ActivitySession $session
    ) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Activity Session Reminder')
            ->view('emails.session-reminder')
            ->with([
                'sessionDate' => $this->session->session_date,
                'startTime'   => $this->session->start_time,
            ]);
    }
}
