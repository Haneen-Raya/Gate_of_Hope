<?php

namespace Modules\CaseManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\CaseManagement\Models\CaseSession;

class UpcomingCaseSessionNotification extends Notification
{
    use Queueable;

    protected CaseSession $session;

    public function __construct(CaseSession $session)
    {
        $this->session = $session;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Mail + database notification
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Upcoming Case Session')
            ->line("You have an upcoming session on {$this->session->session_date}.")
            ->line("Session type: {$this->session->session_type}")
            ->line("Notes: {$this->session->notes}");
    }

    public function toArray($notifiable)
    {
        return [
            'case_session_id' => $this->session->id,
            'session_date' => $this->session->session_date,
            'session_type' => $this->session->session_type,
            'notes' => $this->session->notes,
        ];
    }
}
