<?php

namespace Modules\HumanResources\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class TrainerApprovedNotification
 * * Sent to a user when their application to become a professional trainer
 * is reviewed and accepted by the administration.
 * * * Deliverables:
 * - Standard Email message.
 * - (Expandable) can support database or SMS notifications.
 * * @package Modules\HumanResources\Notifications
 */
class TrainerApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     * * @note Future improvement: Inject the Trainer model to personalize the message.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     * * Currently configured to send via Email.
     * * @param object $notifiable The user receiving the notification.
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     * * Construct an professional email welcoming the trainer to the platform.
     * * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.trainer_approved_subject'))
            ->greeting(__('notifications.hello', ['name' => $notifiable->name]))
            ->line(__('notifications.trainer_approved_body'))
            ->action(__('notifications.go_to_dashboard'), url('/dashboard'))
            ->line(__('notifications.thank_you_for_joining'));
    }

    /**
     * Get the array representation of the notification.
     * * Used if 'database' channel is added to the via() method.
     * * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trainer_approved',
            'message' => 'Your application has been approved.',
        ];
    }
}
