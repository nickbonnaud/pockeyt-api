<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ErrorNotification extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $user;
    public $business;
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $business, $transaction)
    {
        $this->user = $user;
        $this->business = $business;
        $this->transaction = $transaction;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channel = 'error' . $this->business->id;
        return [$channel];
    }
}
