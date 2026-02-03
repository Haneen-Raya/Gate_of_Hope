<?php

namespace Modules\CaseManagement\Console\Commands;

use Illuminate\Console\Command;
use Modules\CaseManagement\Models\CaseSession;
use Modules\CaseManagement\Notifications\UpcomingCaseSessionNotification;


//use : php artisan schedule:run
class NotifyUpcomingSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->toDateString();

        $sessions = CaseSession::whereDate('session_date', $tomorrow)->get();

        foreach ($sessions as $session) {
            if ($session->specialist) {
                $session->specialist->user->notify(new UpcomingCaseSessionNotification($session));
            }
        }

    } 
}
