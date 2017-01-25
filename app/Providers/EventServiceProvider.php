<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CustomerEnterRadius' => [
            'App\Listeners\ShowUser',
        ],
        'App\Events\CustomerLeaveRadius' => [
            'App\Listeners\RemoveUser',
        ],
        'App\Events\RewardNotification' => [
            'App\Listeners\NotifyReward',
        ],
        'App\Events\TransactionsChange' => [
            'App\Listeners\UpdateTransactions',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\Instagram\InstagramExtendSocialite@handle',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
