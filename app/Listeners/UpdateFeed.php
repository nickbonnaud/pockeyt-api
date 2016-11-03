<?php

namespace App\Listeners;

use App\Events\BusinessFeedUpdate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateFeed
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
     * @param  BusinessFeedUpdate  $event
     * @return void
     */
    public function handle(BusinessFeedUpdate $event)
    {
        //
    }
}
