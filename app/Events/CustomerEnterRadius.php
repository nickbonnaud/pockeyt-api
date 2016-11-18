<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerEnterRadius extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $user;
    private $business;
    public $userCheck;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $business, $userCheck)
    {
        $this->user = $user;
        $this->business = $business;
        $this->userCheck = $userCheck;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channel = 'business' . $this->business->id;
        return [$channel];
    }
}
