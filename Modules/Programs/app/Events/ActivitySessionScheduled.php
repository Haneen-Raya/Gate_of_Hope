<?php

namespace Modules\Programs\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Programs\Models\ActivitySession;

class ActivitySessionScheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    /**
     * Create a new event instance.
     */
    public function __construct(ActivitySession $session)
    {
        $this->session = $session;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
