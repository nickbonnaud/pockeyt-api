<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerLeaveRadius extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $user;
    private $prevLocation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $prevLocation)
    {
        $this->user = $user;
        $this->prevLocation = $prevLocation;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channel = 'customerAdd' . $this->prevLocation;
        return [$channel];
    }
}
