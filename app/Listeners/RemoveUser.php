<?php

namespace App\Listeners;

use App\Events\CustomerLeaveRadius;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveUser
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
     * @param  CustomerLeaveRadius  $event
     * @return void
     */
    public function handle(CustomerLeaveRadius $event)
    {
        //
    }
}
