<?php

namespace App\Listeners;

use App\Events\SubscriptionExpiringEvent;
use App\Models\App;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SubscriptionExpiringListener
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
    public function handle(SubscriptionExpiringEvent $event)
    {
        $subscription = $event->subscription;
        $user = $subscription->user;

        $mailHost = config('mail.mailers.smtp.host');
        if(config('app.env') === 'production' || str_contains($mailHost, 'mailtrap')){
            $user->notify((new SubscriptionExpiringNotification($subscription, $subscription->app)));
            $data = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'company_name' => $user->company_name,
                'end_date' => $subscription->getEndDateFormated(),
                'app_data' => App::getData($subscription->app),
            ];
            Mail::to('office@actamedia.rs')->send(new \App\Mail\SubscriptionExpiringMail($data));
        }
    }
}
