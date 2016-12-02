<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerEnterRadius extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $transactions;
    private $business;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transactions, $business)
    {
        $this->transactions = $transactions;
        $this->business = $business;
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
