<?php

namespace Modules\Programs\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\Programs\Emails\SessionReminderMail;
use Modules\Programs\Models\ActivitySession;

/**
 * Class SendSessionReminderMailJob
 * * * Purpose:
 * Offloads the email sending process to a background queue to improve system performance
 * and user experience during session management tasks.
 * * * Behavior:
 * - Implements 'ShouldQueue' for asynchronous execution.
 * - Uses 'SerializesModels' to gracefully handle Eloquent model data within the queue.
 *
 * @package Modules\Programs\Jobs
 */
class SendSessionReminderMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * * Injects the specific ActivitySession that requires reminders.
     *
     * @param ActivitySession $session
     */
    public function __construct(
        public ActivitySession $session
    ) {}

    /**
     * Execute the job.
     *
     * Logic:
     * 1. Fetches all attendance records associated with the session.
     * 2. Eager loads 'beneficiary.user' to minimize database queries (N+1 protection).
     * 3. Validates email existence before attempting dispatch.
     * 4. Sends a specialized 'SessionReminderMail' to each valid recipient.
     *
     * @return void
     */
    public function handle(): void {
        // Retrieve attendances with nested relationship loading for efficiency
        $attendances = $this->session->activityAttendances()?->with('beneficiary.user')->get();

        foreach ($attendances as $attendance) {

            $user = $attendance->beneficiary->user;

            // Integrity Check: Skip if the beneficiary has no associated user account or email
            if (!$user || !$user->email) {
                continue;
            }

            // Dispatch the email using the Mail facade
            Mail::to($user->email)
                ->send(new SessionReminderMail($this->session));
        }
    }
}
