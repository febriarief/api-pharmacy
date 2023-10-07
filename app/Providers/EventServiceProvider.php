<?php

namespace App\Providers;

use App\Models\MasterItem\Item;
use App\Models\Purchase\GoodReceivedDetail;
use App\Models\Sales\Sales;
use App\Models\Sales\SalesDetail;

use App\Observers\MasterItem\ItemObserver;
use App\Observers\Purchase\GoodReceivedDetailObserver;
use App\Observers\Sales\SalesObserver;
use App\Observers\Sales\SalesDetailObserver;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Item::observe(ItemObserver::class);
        GoodReceivedDetail::observe(GoodReceivedDetailObserver::class);
        Sales::observe(SalesObserver::class);
        SalesDetail::observe(SalesDetailObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
