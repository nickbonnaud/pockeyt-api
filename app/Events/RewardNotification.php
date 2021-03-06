<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RewardNotification extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $user;
    public $business;
    public $loyaltyProgram;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $business, $loyaltyProgram)
    {
        $this->user = $user;
        $this->business = $business;
        $this->loyaltyProgram = $loyaltyProgram;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channel = 'reward' . $this->business->id;
        return [$channel];
    }
}
