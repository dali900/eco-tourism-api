<?php

namespace App\Listeners;

use App\Events\FreeTrialExpiredEvent;
use App\Notifications\FreeTrialExpiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FreeTrialExpiredListener
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
     *
     * @param  \App\Events\FreeTrialExpiredEvent  $event
     * @return void
     */
    public function handle(FreeTrialExpiredEvent $event)
    {
        $freeTrial = $event->freeTrial;
        $user = $freeTrial->user;

        if($user->subscription()->count() === 0){
            $mailHost = config('mail.mailers.smtp.host');
            if(config('app.env') === 'production' || str_contains($mailHost, 'mailtrap')){
                $user->notify((new FreeTrialExpiredNotification($freeTrial, $freeTrial->app)));
            }
        }
    }
}
