<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BusinessFeedUpdate extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $transactions;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transactions)
    {
        $this->$transactions = $transactions;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channel = 'business';
        return [$channel];
    }
}
