<?php

namespace App\Listeners;

use App\Events\CustomerInRadius;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserProximity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CustomerInRadius  $event
     * @return void
     */
    public function enter(CustomerInRadius $event)
    {
        console.log($event->user);
    }
}
