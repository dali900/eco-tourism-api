<?php

namespace App\Listeners;

use App\Events\SubscriptionExpiredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\SubscriptionExpiredNotification;
use App\Models\User;
use App\Models\App;
use Illuminate\Support\Facades\Mail;

class SubscriptionExpiredListener
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
     * @param  \App\Events\SubscriptionExpiredEvent  $event
     * @return void
     */
    public function handle(SubscriptionExpiredEvent $event)
    {
        $subscription = $event->subscription;
        $user = $subscription->user;

        $mailHost = config('mail.mailers.smtp.host');
        if(config('app.env') === 'production' || str_contains($mailHost, 'mailtrap')){
            $user->notify((new SubscriptionExpiredNotification($subscription, $subscription->app)));
            $data = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'company_name' => $user->company_name,
                'end_date' => $subscription->getEndDateFormated(),
                'app_data' => App::getData($subscription->app),
            ];
            Mail::to('office@actamedia.rs')->send(new \App\Mail\SubscriptionExpiredMail($data));
        }
    }
}
