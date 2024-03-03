<?php

namespace App\Providers;

use App\Events\FreeTrialExpiredEvent;
use App\Events\FreeTrialStartedEvent;
use App\Events\SubscriptionExpiredEvent;
use App\Events\SubscriptionExpiringEvent;
use App\Listeners\FreeTrialExpiredListener;
use App\Listeners\FreeTrialStartedListener;
use App\Listeners\SubscriptionExpiringListener;
use App\Listeners\SubscriptionExpiredListener;
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
        SubscriptionExpiredEvent::class => [
            //SubscriptionExpiredListener::class
        ],
        SubscriptionExpiringEvent::class => [
            //SubscriptionExpiringListener::class
        ],
        FreeTrialStartedEvent::class => [
            FreeTrialStartedListener::class
        ],
        FreeTrialExpiredEvent::class => [
            FreeTrialExpiredListener::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
