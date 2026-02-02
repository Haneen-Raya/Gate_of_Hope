<?php

namespace Modules\Programs\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProgramAlertMail
 *
 * Represents the structured email notification for Hope Gate system alerts.
 * This class defines the visual envelope (subject) and the content template
 * used to inform Program Managers about critical program status changes
 * or resource shortages.
 *
 * @package Modules\Programs\Emails
 */
class ProgramAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param array $details Data to be injected into the email Blade template.
     */
    public function __construct(public array $details) {}

    /**
     * Get the message envelope.
     *
     * Defines the sender's subject line dynamically based on the alert type.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hope Gate System Notification: ' . ($this->details['title'] ?? 'Alert')
        );
    }

    /**
     * Get the message content definition.
     *
     * Links the mailable to the corresponding Blade view for HTML rendering.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.program_alert'
        );
    }
}
