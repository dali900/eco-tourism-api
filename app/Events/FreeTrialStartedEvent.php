<?php

namespace App\Events;

use App\Models\Plan\FreeTrial;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FreeTrialStartedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $freeTrial;
    public $app;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FreeTrial $freeTrial, $app)
    {
        $this->freeTrial = $freeTrial;
        $this->app = $app;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
