<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TransactionsChange extends Event implements ShouldBroadcast
{
    use SerializesModels;

    private $business;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transaction, $business)
    {
        $this->transaction = $transaction;
        $this->business = $business;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channel = 'transaction' . $this->business->id;
        return [$channel];
    }
}
