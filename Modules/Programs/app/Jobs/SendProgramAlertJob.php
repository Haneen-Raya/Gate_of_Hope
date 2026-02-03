<?php

namespace Modules\Programs\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Programs\Emails\ProgramAlertMail;

/**
 * Class SendProgramAlertJob
 *
 * This job handles the asynchronous delivery of program-related email alerts.
 * By implementing ShouldQueue, it offloads the mailing process to the system's
 * queue worker, preventing latency in the main application flow during
 * resource updates or audits.
 *
 * @package Modules\Programs\Jobs
 */
class SendProgramAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param string $email The recipient's email address (Program Manager).
     * @param array $details Structured data containing program and resource information.
     */
    public function __construct(protected string $email, protected array $details) {}

    /**
     * Execute the job.
     *
     * This method is invoked by the queue worker. It initializes the
     * ProgramAlertMail mailable with the provided details and triggers
     * the dispatch via the Mail facade.
     *
     * @return void
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new ProgramAlertMail($this->details));
    }
}
