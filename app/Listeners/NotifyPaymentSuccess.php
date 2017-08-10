<?php

namespace App\Listeners;

use App\Events\PaymentSuccessNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPaymentSuccess
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
     * @param  PaymentSuccessNotification  $event
     * @return void
     */
    public function handle(PaymentSuccessNotification $event)
    {
        //
    }
}
