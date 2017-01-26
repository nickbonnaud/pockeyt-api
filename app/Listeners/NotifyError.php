<?php

namespace App\Listeners;

use App\Events\ErrorNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyError
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
    public function handle(ErrorNotification $event)
    {
        //
    }
}
