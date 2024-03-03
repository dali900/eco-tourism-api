<?php

namespace App\Listeners;

use App\Events\FreeTrialStartedEvent;
use App\Notifications\FreeTrialStartedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FreeTrialStartedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FreeTrialStartedEvent $event): void
    {
        $freeTrial = $event->freeTrial;
        $app = $event->app;
        $user = $freeTrial->user;

        $mailHost = config('mail.mailers.smtp.host');
        if(config('app.env') === 'production' || str_contains($mailHost, 'mailtrap')){
            $user->notify((new FreeTrialStartedNotification($freeTrial, $app)));
        }
    }
}
