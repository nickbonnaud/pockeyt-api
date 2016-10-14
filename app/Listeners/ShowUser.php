<?php

namespace App\Listeners;

use App\Events\CustomerEnterRadius;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShowUser
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
     * @param  CustomerEnterRadius  $event
     * @return void
     */
    public function handle(CustomerEnterRadius $event)
    {
        //
    }
}
