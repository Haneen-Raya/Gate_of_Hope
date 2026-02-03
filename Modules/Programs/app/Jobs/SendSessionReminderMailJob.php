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

class SendSessionReminderMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param ActivitySession $session
     */
    public function __construct(
        public ActivitySession $session
    ) {}

    /**
     * Execute the job.
     *
     * Sends reminder emails to all beneficiaries
     * registered in the session.
     *
     * @return void
     */
    public function handle(): void {
        $attendances = $this->session->activityAttendances()?->with('beneficiary.user')->get();

        foreach ($attendances as $attendance) {

            $user = $attendance->beneficiary->user;

            if (!$user || !$user->email) {
                continue;
            }

            Mail::to($user->email)
                ->send(new SessionReminderMail($this->session));
        }
    }
}
